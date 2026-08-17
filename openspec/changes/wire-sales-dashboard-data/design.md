## Context

See `proposal.md` — Why. `redesign-sales-dashboard` settled the layout; this change supplies its data.

**What already exists and is not rebuilt:**
- `SalesBonusService::calculate(User, Carbon)` returns the quarter's sales, target, qualified extra, rate, exchange rate, USD bonus, payable BDT, status, and a `configuration_available` flag. It prefers a `FINALIZED`/`PAID` `QuarterlyBonus` snapshot when one exists.
- `BonusFinancialSetting` with `applicableOn(Carbon)` effective-dating, an admin-only controller, form request, view at `/settings/sales-bonus`, and passing tests.
- `SalesTarget::amountFor(int, Carbon)` and `amountsFor(array, Carbon)`.
- `GlobalMonth` for the selected month and its billing-cycle date range.

**Sharp edge:** `bonus_percentage` is stored as a **fraction** — `StoreBonusFinancialSettingRequest` validates `lte:0.25`, and `SalesBonusService` computes `bcmul($extra, $rate)` directly. So `0.01` means 1%. The retired mock used `1.0` to mean 1%. Presentation must multiply by 100; the stored value must never be displayed raw.

## Goals / Non-Goals

**Goals**
- One controller method assembles the whole payload; partials read it and compute nothing.
- Zero and empty states render as legitimate outcomes, not errors.
- `memberPreview()` produces the identical payload shape from fabricated values.

**Non-Goals**
- Changing bonus arithmetic. `SalesBonusService` is consumed as-is.
- A finalize/payout workflow.
- Caching. Correctness first; the trailing-quarter loop is bounded at four calculations.

## Decisions

### 1. Company gross target: override, then derivation

```
Setting::get('company_gross_target')
        │
        ├── set and > 0  ──▶  use it
        │
        └── absent/zero  ──▶  sum of SalesTarget::amountsFor(all sales users, month)
```

Both, as requested. The override wins when present so an admin can state a figure that is not merely the sum of individual quotas; the derivation means the number is never absent just because nobody set it. Stored in the existing `settings` key-value table under the `company` group — that group already exists in `settings/index.blade.php`, so no migration and no new settings page.

*Alternative considered:* a dedicated table with effective dating, mirroring `BonusFinancialSetting`. Rejected as disproportionate for a single scalar; if company targets later need history, that is its own change.

### 2. Earnings history computes on the fly

Four calls to `SalesBonusService::calculate()`, one per trailing quarter. `QuarterlyBonus` is empty and only populates on `finalize()`, so reading it would leave the section blank indefinitely.

The service already prefers a finalized snapshot when one exists, so this path upgrades itself for free once a finalize workflow arrives — no rework needed here.

*Accepted trade-off:* a past quarter's figure can move if an old order is edited. Acceptable while nothing is finalized; the moment finalize exists, snapshots take over.

### 3. Unconfigured bonus shows zero plus a note

`calculate()` already returns `configuration_available: false` with zero amounts. The hero renders the zero and adds a note that rates are not configured yet and the bonus will apply once they are. Sales, target, achievement, and extra sales remain live throughout — they do not depend on bonus configuration.

Administrators additionally get a link to `/settings/sales-bonus`. A salesperson cannot act on it, so they do not see the link.

*Rejected:* seeding a default configuration. Those values drive real payouts; inventing them is not the software's call.

### 4. One payload builder, shared with the preview

```
index()          ──┐
                   ├──▶  dashboardPayload(...)  ──▶  view('sales.dashboard')
memberPreview()  ──┘        (same array shape)
```

`memberPreview()` builds the same keys from fabricated values rather than calling the real calculators. This keeps the view free of `isset()` branches and stops the preview drifting from the real page — the existing `SalesMemberPreviewTest` guards it.

### 5. Quarter months are computed individually

For each of the quarter's three calendar months: resolve its billing period via `GlobalMonth::periodFor()`, sum the viewer's credit, read `SalesTarget::amountFor()`, then derive achievement, extra (clamped at zero), remaining, and status.

Status derivation:

| Condition | Status |
|---|---|
| month is in the future | `upcoming` |
| sales ≥ target and month is the selected one | `bonus-active` |
| sales ≥ target | `achieved` |
| otherwise | `in-progress` |

### 6. Percentages and division guards

Every percentage is computed only when its denominator is greater than zero, and is otherwise `null`. Partials render `null` as an em dash rather than `0%`, so "no target recorded" never reads as "achieved nothing".

## Risks / Trade-offs

- **Query volume** — the trailing-quarter history triggers four `calculate()` calls, each looping three months of order queries → up to ~12 order queries per page load. → Bounded and indexed on `created_at`; measure before optimising. If it bites, cache per user/quarter.
- **Billing-cycle months are not calendar months** — `GlobalMonth::periodFor()` may span two calendar months. Quarter month boundaries must use it consistently, exactly as `SalesBonusService::monthlyTotals()` already does, or dashboard and payslip will disagree. → Reuse the same helper; do not recompute ranges by hand.
- **`decimal:6` casts return strings** — `bonus_percentage` and money fields arrive as strings from Eloquent. → Cast explicitly at the payload boundary; never pass raw casts into `number_format()`.
- **Admin viewing the page holds no sales credit** — an admin sees zeros for personal figures. → Admin already reaches this page; the existing group/personal distinction in the controller decides whose numbers are shown.
- **Tests currently assert fixed mock strings** — every assertion in `SalesDashboardAnalyticsTest` is tied to mock values. → Rewrite against seeded orders and targets, asserting computed outcomes rather than literals.

## Migration Plan

No data migration. `company_gross_target` is absent until an admin sets it, and absence is a supported state.

1. Add the company-target resolution and the settings entry.
2. Build the payload method; wire `index()`.
3. Convert partials to the payload.
4. Mirror the shape in `memberPreview()`.
5. Remove `$mock`.
6. Rewrite tests against seeded data.
7. Verify responsive and dark-mode behaviour on the live page (carried over from `redesign-sales-dashboard` tasks 7.2–7.3).

## Open Questions

- Should earned-to-date span exactly four trailing quarters or all recorded history? Four is implemented; widening it is a query change, not a layout change.
