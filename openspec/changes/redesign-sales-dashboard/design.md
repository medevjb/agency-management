## Context

See `proposal.md` — Why. This section records only the constraints that shape the approach.

**The page is already mid-transplant.** `SalesDashboardController@index` computes a rich real dataset (`$trend`, `$bonus`, `$pipeline`, `$topClients`, `$memberBreakdown`, `$selectedMonth`, `$availableMonths`), but `dashboard.blade.php` renders entirely from a hardcoded `$mock` array in an inline `@php` block and references none of it. Phase 1 keeps it that way; the controller is not touched.

**Existing design system.** Tailwind v3 + Alpine v3 + Font Awesome, with a page-scoped `@push('styles')` block in `dashboard.blade.php` defining `s-`-prefixed classes (`s-panel`, `s-eyebrow`, `s-num`, `s-badge`, `s-zone`, `s-share`, `s-formula`, `s-callout`, `s-icon-chip`). Dark mode is a `.dark` class ancestor, not a media query. These conventions are reused, not replaced.

**Typography is locked.** `Figtree` is applied with `!important` at `resources/views/layouts/dashboard.blade.php:282,384,454`. A display typeface is not available as a differentiator; scale, weight, and tabular numerals are.

**Two time scales collide.** `GlobalMonth` stores one session-wide month (set from the navbar picker in `components/header.blade.php`) that every dashboard reads. `SalesBonusService::calculate()` is quarter-scoped: it derives the quarter from the date, sums each month's *positive-only* excess over target, then applies the bonus rate **once** to that total. So the page's selected period is a month while its hero figure is a quarter.

## Goals / Non-Goals

**Goals**
- Each figure renders in exactly one place, chosen as the place it carries the most meaning.
- A reader can trace sales → taka without leaving the top of the page.
- Past quarters are reachable, and their payouts are legible as payments rather than statistics.
- The markup is structured so Phase 4 can substitute real values without re-laying out the page.

**Non-Goals**
- Interactive period navigation beyond the existing global month picker. History is presented, not filtered.
- Charting libraries. The sparkline is CSS/inline SVG over the existing token set.
- Reconciling the month-vs-quarter scope mismatch in code. Phase 1 states it in the header; resolving it is a later decision.

## Decisions

### 1. Lead with the BDT figure; sales becomes its derivation

The hero is `৳34,912.50` with the derivation chain beneath it. Sales, target, and achievement follow in a second region.

*Alternative considered:* lead with sales, per the original brief's hierarchy (personal sales 1st, bonus 4th). Rejected because the page's single job is answering "how much am I making?", and a sales-first order makes the reader assemble that answer themselves. The conventional order is what produced the generic-analytics feel in the first place.

### 2. Remove the KPI card row entirely

*Alternative considered:* keep two of four (My Sales, Bonus). Rejected — a two-card strip still restates the hero and the progress bar, and retaining a partial grid preserves the visual grammar the redesign is trying to leave. `kpi-cards.blade.php` is removed from this page's composition; the file is deleted only after confirming no other view includes it.

### 3. The hinge: extra sales is the only figure belonging to both halves

```
     PERFORMANCE                   ╎                EARNINGS
   "what I sold"                   ╎           "what I get paid"
                                   ╎
 sales ── vs target ── EXTRA SALES ─┼─ × rate ── USD ── × FX ── ৳
                            ▲      ╎
                    the single shared value
```

This governs placement: anything left of the hinge appears once in the month-progress region; anything right appears once in the hero. Extra sales appears at the seam of both — as the `+$8,500` annotation on the progress bar, and as `qualified extra` entering the derivation.

### 4. Trend and payout history are one object, not two

Twelve monthly bars grouped into four quarter brackets:

```
  EARNED TO DATE                                     ৳81,687.50
  ────────────────────────────────────────────────────────────
       ▁ ▂ ▃      ▄ ▅ ▃      ▆ ▇ ▅      ▇ █ ▇
     └─ Q4 '25 ─┘└─ Q1 '26 ─┘└─ Q2 '26 ─┘└─ Q3 '26 ─┘
        ৳6,125     ৳12,250     ৳28,400     ৳34,912
         PAID        PAID        PAID      ACCRUING
```

Bars carry trajectory; brackets carry payout history; the right-hand total carries lifetime earnings. The grouping also *teaches the rule* — qualification is monthly (bars), payment is quarterly (brackets) — which no amount of explanatory copy does as well.

*Alternative considered:* separate trend chart and payout list. Rejected as reintroducing the density the change exists to remove.

### 5. Containment encodes importance

```
  hero       no border, no background — sits on the page
  month      hairline rule above only
  quarter    hairline rule above only
  history    hairline rule above only
  company    hairline rule, muted text
```

Uniform `.s-panel` cards flatten hierarchy: every section claims equal weight, so the reader has no entry point. Removing chrome from the hero and adding it progressively outward makes the reading order structural rather than positional. `.s-panel` is retained for the lower regions so the page still reads as native to the application.

*Risk of over-correction:* if the hero looks unfinished against a `bg-gray-100` page background, add a subtle background wash rather than a full bordered panel — the distinction to preserve is *the hero is not a widget*.

### 6. Semantic colour, reduced to three accents

| Token | Light | Dark | Means |
|---|---|---|---|
| emerald | `#059669` | `#34d399` | money earned — taka, bonus, extra sales |
| indigo | `#4f46e5` | `#a5b4fc` | the goal — target only |
| amber | `#d97706` | `#fcd34d` | accruing / in progress |
| gray | scale | scale | context, labels, past, company |

Blue and rose are dropped from this page. Today the same five accents carry no consistent meaning (indigo is both "primary" and "target"; emerald is both "success" and "money"). Binding one hue to one concept lets colour do work the labels currently duplicate. The page's accent identity shifts from indigo to emerald, matching what the page is about.

### 7. Type scale

Existing `s-num` (tabular numerals) and `s-eyebrow` (0.6875rem / 800 / uppercase / 0.08em) are kept unchanged. The hero figure moves to roughly `clamp(2.75rem, 6vw, 4rem)` at weight 800 with `-0.03em` tracking. The contrast between a ~4rem figure and a 0.6875rem label is the entire type story — appropriate for a payout statement and achievable within `Figtree`.

### 8. Per-month bonus amounts are not displayed

The quarter strip shows each month's **extra sales** (`+$5,000`, `+$8,500`) and its status. Taka appears only at quarter level.

The current mock invents a per-month bonus (July `৳6,125`, August `৳10,412`) that the real service cannot produce — it applies the rate once to the quarter's summed excess. Rendering a per-month figure now guarantees a reconciliation complaint in Phase 4. Showing extra sales is both honest and the more useful number, since extra sales is what the salesperson influences.

### 9. Mock data stays centralized and shaped like the eventual real payload

The `$mock` array remains the single source for the page, extended with `history` (12 monthly points + 4 quarter groups + lifetime total). Keys mirror what `SalesBonusService` and the controller already return (`qualified_extra_sales`, `bonus_percentage`, `exchange_rate`, `bonus_payment_amount`, `status`) so Phase 4 is a substitution rather than a re-shape.

*Open consideration:* whether `$mock` moves out of the inline `@php` block into a dedicated location. Deferred — it does not affect layout or the task breakdown.

### 10. Component structure

```
sales/dashboard.blade.php          composition + mock data
  partials/header                  title, period, welcome
  partials/bonus-hero              NEW — taka figure, derivation chain, motivation callout
  partials/month-progress          target zone / bonus zone bar  (from target-progress)
  partials/quarter-performance     three months, extra sales, quarter summary
  partials/earnings-history        NEW — sparkline + quarter brackets + lifetime total
  partials/company-summary         one line  (from company-performance)
  partials/status-badge            unchanged — all five statuses already supported
```

`status-badge.blade.php` needs no change: it already covers Target Achieved, Bonus Active, In Progress, Upcoming, and Accruing with badge + icon + text, satisfying the "not colour alone" requirement.

## Risks / Trade-offs

- **Departs from the written Phase 1 brief** (four KPI cards; sales-first hierarchy) → Both departures are deliberate, recorded here and in the proposal, and were confirmed with the change owner before this document was written.
- **Removing card chrome from the hero may read as unfinished** → Mitigation in Decision 5; escalate to a background wash, not a bordered panel.
- **The history region is entirely mock in Phase 1** → Shape it against `QuarterlyBonus` fields (`year`, `quarter`, `bonus_payment_amount`, `status`) that already exist, so Phase 4 wiring is mechanical. Lifetime total has no backing query yet and will need one.
- **Deleting `kpi-cards.blade.php` could break another view** → Grep for includes across `resources/views/` before deleting; remove usage from this page regardless, delete the file only if unreferenced.
- **`sales.memberPreview` renders the same view** → Its fabricated `$mine`/`$group`/`$trend` payload must still render without error under the new composition, and `SalesMemberPreviewTest` must keep passing.
- **Feature tests assert on rendered strings** → `SalesDashboardAnalyticsTest` asserts `assertSee('My Sales')`, `assertSee('$128,500')` and similar. Removing the KPI row breaks these by design; assertions move to the new structure in the same change rather than being deleted.
- **Reduced-motion and focus states** → Any sparkline reveal or hero animation must respect `prefers-reduced-motion`, and the page must remain keyboard-navigable with visible focus.

## Migration Plan

Presentation-layer only; no data migration and no rollback procedure beyond reverting the commit. Sequence:

1. Extend `$mock` with the `history` shape.
2. Add `bonus-hero` and `earnings-history` partials.
3. Restructure `target-progress` → `month-progress`, `company-performance` → `company-summary`.
4. Recompose `dashboard.blade.php`; drop the `kpi-cards` include.
5. Move colour and containment rules into the page's `@push('styles')` block.
6. Update the sales feature tests; run them plus `SalesMemberPreviewTest` and `SalesRoleTest`.
7. Verify responsive behaviour at mobile/tablet/desktop with no horizontal scroll, and dark mode across all regions.

## Open Questions

- Does the lifetime *earned to date* total span all time or a bounded window (e.g. current financial year)? Affects a Phase 4 query, not this layout.
- Should the earnings-history sparkline show sales or bonus per month? Bars are illustrative in Phase 1 either way; the choice can be made when real data is wired.
