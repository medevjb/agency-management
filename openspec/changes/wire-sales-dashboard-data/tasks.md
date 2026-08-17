## 1. Company target configuration

- [x] 1.1 Add company gross target resolution: an admin-set `company_gross_target` setting when present, otherwise the sum of all salespeople's targets for the period
- [x] 1.2 Expose `company_gross_target` in the admin settings `company` group so an admin can set and change it
- [x] 1.3 Confirm non-admins cannot set it (the settings routes are already admin-gated)

## 2. Controller payload

- [x] 2.1 Add a payload builder on `SalesDashboardController` that assembles period, currentMonth, bonus, months, quarter, history, and company in one place
- [x] 2.2 Derive period (month name, year, quarter, quarter month names) from `GlobalMonth`
- [x] 2.3 Compute the current month's sales, target, achievement, extra sales, shortfall, and achieved flag from the viewer's sales credit
- [x] 2.4 Read the quarter bonus from `SalesBonusService::calculate()`, converting the stored fraction rate to a display percentage
- [x] 2.5 Compute the quarter's three months individually with status derivation (upcoming / bonus-active / achieved / in-progress)
- [x] 2.6 Build trailing four-quarter history with per-quarter bonus and an earned-to-date total
- [x] 2.7 Compute company gross sales, target, achievement, and the viewer's contribution
- [x] 2.8 Guard every percentage against a zero denominator, returning null rather than zero

## 3. Views

- [x] 3.1 Remove the `$mock` array from `dashboard.blade.php` and pass the controller payload to the partials
- [x] 3.2 Update `bonus-hero` to read live bonus values and render the unconfigured note when no configuration applies
- [x] 3.3 Show admins a link to the bonus settings page when configuration is missing; do not show it to salespeople
- [x] 3.4 Update `month-progress`, `quarter-performance`, `earnings-history`, `company-summary`, and `header` to read the payload
- [x] 3.5 Render null percentages as an em dash rather than 0%

## 4. Member preview

- [x] 4.1 Rebuild `memberPreview()` to produce the same payload shape from fabricated values
- [x] 4.2 Confirm `SalesMemberPreviewTest` passes unchanged in intent

## 5. Tests

- [x] 5.1 Rewrite `SalesDashboardAnalyticsTest` against seeded orders, targets, and bonus configuration instead of mock literals
- [x] 5.2 Test the company target override: admin-set value wins, sum-of-targets fallback otherwise, zero when neither
- [x] 5.3 Test the unconfigured-bonus state: zero shown, note present, sales figures still live, note gone once configured
- [x] 5.4 Test that a stored rate of 0.01 displays as 1%
- [x] 5.5 Test extra sales excludes shortfall months and never goes negative
- [x] 5.6 Test earnings history totals the trailing quarters and marks the current one as accruing
- [x] 5.7 Test the empty case: a salesperson with no sales renders the page without error
- [x] 5.8 Run the full sales feature test suite

## 6. Verification

- [ ] 6.1 Verify responsive behaviour at mobile, tablet, and desktop with no horizontal scrolling — structurally verified (mobile-first media queries, minmax(0,1fr) grids); not confirmed at a true mobile viewport
- [x] 6.2 Verify dark mode across every region
- [x] 6.3 Confirm no change to `SalesBonusService` or the bonus settings controller
- [x] 6.4 Run `vendor/bin/pint --dirty --format agent`
