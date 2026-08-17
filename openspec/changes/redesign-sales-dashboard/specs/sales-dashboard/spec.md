## Purpose

Presents an individual salesperson's earnings: how much they sold, whether they beat their monthly target, what bonus that has earned in the payout currency, and what they have been paid historically. The page exists to answer "how much money am I making?" rather than to serve general sales analytics.

## ADDED Requirements

### Requirement: Earnings lead the page

The dashboard SHALL present the accrued bonus in the payout currency (BDT) as the first and visually dominant figure, before any sales, target, or achievement figure.

#### Scenario: Salesperson opens the dashboard

- **WHEN** a sales member loads the dashboard
- **THEN** the accrued quarterly bonus is shown in BDT as the page's largest figure
- **AND** its accrual status is shown as a badge carrying both an icon and text

#### Scenario: Sales figures are subordinate

- **WHEN** the page renders
- **THEN** the monthly sales figure, target, and achievement percentage appear below the bonus figure, not above it

### Requirement: The bonus derivation is transparent

The dashboard SHALL show every step converting qualified extra sales into the payable BDT amount, so a salesperson can reproduce the figure without assistance.

#### Scenario: Derivation chain is visible

- **WHEN** the bonus figure is displayed
- **THEN** the qualified extra sales amount, the bonus rate, the resulting USD amount, and the USD-to-BDT rate are all shown
- **AND** the arithmetic linking them is presented in reading order from qualified extra sales through to the final BDT amount

#### Scenario: Marginal earning is stated

- **WHEN** the bonus is active
- **THEN** the page states what a stated additional amount of qualified sales would add to the bonus in BDT

### Requirement: No section restates another section's primary figure

The dashboard SHALL NOT present a value purely as a restatement of one shown elsewhere. A figure MAY appear in a second section when that section frames it differently — a month inside the quarter ledger it belongs to is context, not repetition — but no section may exist whose content is wholly derived from figures already shown.

#### Scenario: No summary row of already-shown figures

- **WHEN** the page renders
- **THEN** there is no KPI card, summary strip, or stat block whose every value is drawn from another section
- **AND** the monthly sales amount, target, and achievement percentage each appear at most twice: once in monthly progress and once within the quarter ledger

#### Scenario: Superseded summary cards are absent

- **WHEN** the page renders
- **THEN** no "Target Achievement", "Bonus Accrued", or month-over-month comparison card is present

### Requirement: Target progress distinguishes target from bonus territory

The dashboard SHALL show monthly progress on a single track that visually separates sales counting toward the target from sales exceeding it.

#### Scenario: Target exceeded

- **WHEN** monthly sales exceed the monthly target
- **THEN** the track shows a target segment and a distinct bonus segment beyond it
- **AND** the amount above target is labelled as extra sales
- **AND** a "Target Achieved" status is shown

#### Scenario: Target not yet reached

- **WHEN** monthly sales are below the monthly target
- **THEN** no bonus segment is shown
- **AND** the page states the additional sales amount required to unlock the bonus
- **AND** no negative extra-sales figure is displayed

### Requirement: Quarter view shows monthly qualification

The dashboard SHALL present the three months of the current quarter, showing that bonus qualification is determined month by month.

#### Scenario: Each month is shown with its qualification outcome

- **WHEN** the quarter section renders
- **THEN** each of the three months shows its sales, target, achievement percentage, and status
- **AND** a month that exceeded target shows its extra sales amount
- **AND** a month below target shows the remaining amount instead of a negative extra-sales value

#### Scenario: Bonus is not attributed to individual months

- **WHEN** the quarter section renders
- **THEN** no per-month BDT bonus amount is shown
- **AND** BDT bonus amounts appear only at quarter level

#### Scenario: Quarter totals do not imply qualification

- **WHEN** the quarter summary renders
- **THEN** it presents quarter sales, target, achievement, and qualified extra sales as an aggregate
- **AND** the page indicates that quarter achievement does not determine bonus qualification

### Requirement: Historical earnings are reachable

The dashboard SHALL let a salesperson see past quarters and their payouts without leaving the page.

#### Scenario: Past quarter payouts are listed

- **WHEN** the earnings history renders
- **THEN** each past quarter is shown with its BDT payout amount and its payment status
- **AND** the current quarter is distinguishable from settled ones by its status

#### Scenario: Trajectory is visible

- **WHEN** the earnings history renders
- **THEN** month-level performance across the trailing period is shown
- **AND** those months are visually grouped by the quarter they belong to

#### Scenario: Lifetime earnings are totalled

- **WHEN** the earnings history renders
- **THEN** a cumulative total of bonus earned across the shown quarters is displayed

### Requirement: Company performance is context, not competition

The dashboard SHALL present company-level figures at lower visual weight than the salesperson's own figures.

#### Scenario: Company summary is subordinate

- **WHEN** the page renders
- **THEN** company gross sales, company target, company achievement, and the salesperson's contribution percentage are shown
- **AND** they occupy less visual weight than the bonus and monthly progress sections

### Requirement: The viewing period is unambiguous

The dashboard SHALL state which month is being viewed and which quarter the bonus figure covers.

#### Scenario: Period is stated in the header

- **WHEN** the page renders
- **THEN** the header shows the selected month and its quarter
- **AND** the page indicates that the bonus accrues across the whole quarter rather than the selected month alone

### Requirement: Status is conveyed without relying on colour

The dashboard SHALL communicate every status through text accompanied by an icon, never through colour alone.

#### Scenario: Statuses carry text and icon

- **WHEN** any of the statuses Target Achieved, Bonus Active, In Progress, Upcoming, or Accruing is shown
- **THEN** the status renders with a text label and an icon

### Requirement: Currency is distinguished by role

The dashboard SHALL present sales figures in USD and the payable bonus in BDT, with the BDT amount given greater visual prominence.

#### Scenario: Currencies are not conflated

- **WHEN** the page renders
- **THEN** sales, target, and extra sales amounts carry the USD symbol
- **AND** payable bonus amounts carry the BDT symbol

### Requirement: The page is responsive without horizontal scrolling

The dashboard SHALL adapt to mobile, tablet, and desktop widths.

#### Scenario: Narrow viewport

- **WHEN** the page is viewed below tablet width
- **THEN** multi-column regions stack into a single column
- **AND** the page does not scroll horizontally

#### Scenario: Dark mode

- **WHEN** the application is in dark mode
- **THEN** every region of the page renders with dark-mode styling consistent with the rest of the application
