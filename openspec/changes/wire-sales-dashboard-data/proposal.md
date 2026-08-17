## Why

`redesign-sales-dashboard` established the page's structure against a hardcoded `$mock` array. Every figure a salesperson sees is fabricated. The data to replace it already exists — `SalesBonusService`, `SalesTarget`, `Order`/`OrderSalesShare`, and the admin-managed `BonusFinancialSetting` — but the view reads none of it.

## What Changes

- Replace the `$mock` array entirely. `SalesDashboardController@index` supplies every figure the redesigned page renders.
- Derive the period (month, year, quarter, quarter month names) from `GlobalMonth` rather than hardcoded strings, so the navbar month picker drives the page.
- Compute the current month's sales, target, achievement, extra sales, and shortfall from the viewer's own sales credit.
- Read the quarter bonus from `SalesBonusService::calculate()` — qualified extra, rate, USD amount, exchange rate, and payable BDT.
- Compute the quarter's three months individually, each with its own qualification outcome.
- Build earnings history by running the bonus calculation over the trailing four quarters, plus a lifetime earned-to-date total.
- **Company gross target resolves in two steps**: an admin-set value when present, otherwise the sum of every salesperson's individual target for the period.
- Add a `company_gross_target` setting to the existing admin settings store so an admin can override the derived figure.
- Show a plainly-worded note when no `BonusFinancialSetting` is configured, alongside the zero it currently produces, so a zero bonus is never mistaken for a calculation result.
- Rework `sales.memberPreview` to build the same payload shape from fabricated values, so the preview keeps working without the view carrying mock data.

### Non-goals

No change to how bonus is calculated — `SalesBonusService` is read, not modified. No finalize/payout workflow. No change to the page's visual structure, which `redesign-sales-dashboard` settled.

## Capabilities

### New Capabilities
- `sales-bonus-configuration`: How the company gross target is resolved and what a salesperson sees while bonus rates are unconfigured.

### Modified Capabilities
- `sales-dashboard`: Every figure becomes live and period-driven rather than static; the page gains an unconfigured-bonus state and a real earnings history.

## Impact

**Changed**
- `app/Http/Controllers/SalesDashboardController.php` — builds the dashboard payload; `memberPreview()` mirrors its shape
- `resources/views/sales/dashboard.blade.php` — `$mock` removed
- `resources/views/sales/partials/*.blade.php` — read controller data
- `tests/Feature/SalesDashboardAnalyticsTest.php` — assertions move from fixed mock strings to seeded data

**Unchanged**
- `app/Services/SalesBonusService.php`, `app/Models/BonusFinancialSetting.php`, `app/Http/Controllers/BonusFinancialSettingController.php` — the admin bonus controls already exist and are tested
- Database schema. `company_gross_target` uses the existing `settings` key-value table; no migration.

**Prerequisite**
`redesign-sales-dashboard` — this change replaces the mock data that change introduced.
