## Why

The salesperson dashboard at `/sales/dashboard` reads as a generic analytics page with a bonus section bolted on, when its actual job is to answer one question: *how much money am I making?* Three concrete problems follow from that mismatch:

1. **Repetition, not density.** Four numbers are restated across the page — `$128,500` appears 5 times, `107.1%` and `$120,000` 4 times each, `$8,500` 3 times. Nothing reads as *the* answer because everything is a restatement.
2. **No access to past performance.** Bonus qualifies monthly and pays quarterly, but a salesperson cannot reach a single past quarter — the payout they actually received. There is no lifetime earnings figure anywhere.
3. **The strongest content is buried.** The bonus derivation (`qualified extra × rate → USD × FX → BDT`) is the clearest thing on the page and sits in a bottom-left panel at equal weight to company statistics.

## What Changes

Frontend only. This is Phase 1 of a multi-phase effort: establish the complete visual structure against mock data before any business logic is built.

- **BREAKING (visual)** — Remove the four-card KPI row. Every value it carries is shown more meaningfully elsewhere; it is the single largest source of repetition. This departs from the original Phase 1 brief, which mandated four KPI cards.
- Invert the information hierarchy to lead with the BDT bonus figure. Sales, target, and achievement become its derivation rather than the page's opening statement.
- Reduce the page from roughly 20 boxed regions to five, with each figure appearing exactly once.
- Add a combined earnings-history region: monthly sparkline bars grouped into quarter brackets, each bracket labelled with its payout and status, plus a lifetime *earned to date* total. One object answers both "am I trending up?" and "what have I been paid?".
- Encode importance through containment rather than uniform cards — the hero sits directly on the page background with no panel; peripheral sections gain progressively more chrome.
- Reduce the page palette from five accent colours to a semantic three (emerald = money earned, indigo = target, amber = accruing) plus grays. Blue and rose are dropped from this page.
- Demote company performance to a single quiet summary line.
- Show per-month **extra sales** rather than per-month bonus amounts. `SalesBonusService::calculate()` applies the bonus rate once to the quarter's summed excess; there is no per-month bonus figure, and displaying one would not reconcile against a payslip once real data arrives in a later phase.
- Make the viewing period explicit in the header, including that bonus accrues across the full quarter while the global picker selects a month.

### Non-goals

No backend logic, database schema, bonus APIs, admin bonus settings, or real bonus calculations. All values remain mock. The controller is not modified — data it already computes but the view ignores (`$trend`, `$pipeline`, `$topClients`, `$memberBreakdown`) stays untouched for later phases to wire up or remove.

## Capabilities

### New Capabilities
- `sales-dashboard`: What an individual salesperson's dashboard presents — the earnings hero and its derivation, monthly target progress, quarter qualification, earnings history, and company context; plus the rules governing which period each figure belongs to.

### Modified Capabilities
<!-- None. `openspec/specs/` is currently empty; this change introduces the first capability. -->

## Impact

**Changed**
- `resources/views/sales/dashboard.blade.php` — page composition and the centralized mock dataset
- `resources/views/sales/partials/*.blade.php` — existing partials restructured; `kpi-cards.blade.php` removed from this page
- `tests/Feature/SalesDashboardAnalyticsTest.php` and sibling sales feature tests — assertions follow the new structure

**Unchanged**
- `app/Http/Controllers/SalesDashboardController.php`
- `app/Services/SalesBonusService.php`, `app/Support/GlobalMonth.php`
- Database schema, migrations, routes, layout, navigation, authentication

**Constraints discovered**
- `Figtree` is enforced with `!important` in `resources/views/layouts/dashboard.blade.php` (lines 282, 384, 454), so typographic differentiation must come from scale and weight rather than typeface choice.
- The global month picker in `resources/views/components/header.blade.php` sets a session-wide month, while the bonus wallet is quarter-scoped — a mismatch the page must state rather than hide.

**Next phase**
Backend bonus calculation and data model.
