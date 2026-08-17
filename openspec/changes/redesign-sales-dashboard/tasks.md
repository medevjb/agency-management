## 1. Mock data

- [x] 1.1 Extend the centralized `$mock` array in `dashboard.blade.php` with a `history` shape: twelve monthly points (label, sales, target), four quarter groups (label, payout BDT, status), and a lifetime `earnedToDate` total
- [x] 1.2 Rename mock bonus keys to mirror the eventual real payload (`qualifiedExtra`, `rate`, `exchangeRate`, `bonusUsd`, `bonusBdt`, `status`) so Phase 4 is a substitution rather than a re-shape
- [x] 1.3 Remove the per-month `bonusBdt` values from `mock.months`, keeping per-month `extra` and `remaining`

## 2. Style tokens

- [x] 2.1 Add hero type scale to the page style block: a bonus figure at `clamp(2.75rem, 6vw, 4rem)`, weight 800, `-0.03em` tracking, tabular numerals
- [x] 2.2 Add the containment rules — a chrome-free hero region and hairline-ruled sections for month, quarter, history, and company
- [x] 2.3 Add sparkline and quarter-bracket styles for the earnings-history region
- [x] 2.4 Remove the now-unused blue and rose accent variants from this page's style block, keeping emerald, indigo, amber, and gray

## 3. New partials

- [x] 3.1 Create `partials/bonus-hero.blade.php` — BDT figure, accrual status badge, derivation chain (qualified extra → rate → USD → FX → BDT), and the marginal-earning callout
- [x] 3.2 Create `partials/earnings-history.blade.php` — monthly sparkline bars grouped into quarter brackets, each bracket labelled with payout and status, plus the lifetime earned-to-date total

## 4. Restructure existing partials

- [x] 4.1 Rework `target-progress.blade.php` into `month-progress.blade.php` — keep the target/bonus zone track, drop the three duplicated sub-cards and the duplicated achievement badge, annotate extra sales on the track
- [x] 4.2 Update `quarter-performance.blade.php` — show per-month extra sales instead of per-month bonus, keep the quarter summary and its aggregate-not-trigger note
- [x] 4.3 Rework `company-performance.blade.php` into `company-summary.blade.php` as a single muted line
- [x] 4.4 Update `header.blade.php` to state the selected month, its quarter, and that bonus accrues across the whole quarter

## 5. Recompose the page

- [x] 5.1 Update `dashboard.blade.php` to compose header → bonus-hero → month-progress → quarter-performance → earnings-history → company-summary
- [x] 5.2 Remove the `kpi-cards` include
- [x] 5.3 Confirm `kpi-cards.blade.php` is referenced nowhere else, then delete it
- [x] 5.4 Delete the superseded `target-progress.blade.php` and `company-performance.blade.php` once their replacements render

## 6. Tests

- [x] 6.1 Update `SalesDashboardAnalyticsTest` assertions to the new structure — bonus hero, derivation chain, month progress, quarter qualification
- [x] 6.2 Add assertions covering the earnings-history region: past quarter payouts, statuses, and the lifetime total
- [x] 6.3 Add an assertion that no per-month BDT bonus amount is rendered in the quarter section
- [x] 6.4 Verify `SalesMemberPreviewTest` still passes — `memberPreview()` renders the same view with a fabricated payload
- [x] 6.5 Run the sales feature tests and confirm all pass

## 7. Verification

- [x] 7.1 Confirm each of monthly sales, target, achievement, and extra sales renders exactly once
- [ ] 7.2 Check responsive behaviour at mobile, tablet, and desktop widths with no horizontal scrolling (deferred to wire-sales-dashboard-data: verify once on the live page)
- [ ] 7.3 Check dark mode across every region (deferred to wire-sales-dashboard-data: verify once on the live page)
- [x] 7.4 Confirm no backend, migration, route, or model file was modified
- [x] 7.5 Run `vendor/bin/pint --dirty --format agent`
