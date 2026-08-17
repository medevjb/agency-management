## ADDED Requirements

### Requirement: Every figure is derived from recorded data

The dashboard SHALL calculate every value it presents from orders, sales credit, targets, and bonus configuration. No figure may be hardcoded in the presentation layer.

#### Scenario: Salesperson with recorded sales

- **WHEN** a salesperson holds sales credit in orders within the viewed period
- **THEN** the monthly sales figure equals the sum of their credit in that period
- **AND** the monthly target is the target recorded for them for that month
- **AND** achievement is their sales as a percentage of that target

#### Scenario: Salesperson with no recorded sales

- **WHEN** a salesperson holds no sales credit in the viewed period
- **THEN** the monthly sales figure is zero
- **AND** the page renders without error

### Requirement: The viewed period drives the page

The dashboard SHALL present figures for the globally selected month, and SHALL derive the quarter shown from that month.

#### Scenario: Selecting a different month

- **WHEN** a salesperson selects a different month from the global month picker
- **THEN** the monthly figures reflect that month
- **AND** the quarter section reflects the quarter that month falls in

#### Scenario: Period is labelled from the selection

- **WHEN** the page renders
- **THEN** the header names the selected month, its year, and its quarter
- **AND** the quarter's constituent month names are stated

### Requirement: Extra sales counts only months that beat their target

The dashboard SHALL treat qualified extra sales as the sum, across the quarter's months, of each month's sales above its target, counting no negative amounts.

#### Scenario: Mixed quarter

- **WHEN** one month exceeds its target and another falls short
- **THEN** qualified extra sales includes only the excess from the month that exceeded
- **AND** the shortfall month contributes zero rather than a negative amount

#### Scenario: Month without a recorded target

- **WHEN** a month has no target recorded
- **THEN** that month contributes no qualified extra sales

### Requirement: Earnings history is computed across trailing quarters

The dashboard SHALL present the trailing four quarters, each with its bonus outcome, and a cumulative total across them.

#### Scenario: History is populated without a finalize step

- **WHEN** a salesperson has recorded sales in past quarters
- **THEN** each of the trailing four quarters shows its computed bonus amount
- **AND** the earned-to-date total is the sum of those amounts

#### Scenario: Current quarter is distinguished

- **WHEN** the history renders
- **THEN** the quarter containing the selected month is marked as still accruing
- **AND** earlier quarters are marked as settled

#### Scenario: New salesperson

- **WHEN** a salesperson has no sales in any trailing quarter
- **THEN** each quarter shows zero
- **AND** the earned-to-date total is zero

### Requirement: Company figures are computed company-wide

The dashboard SHALL derive company gross sales from all recorded orders in the period, and the salesperson's contribution from their share of it.

#### Scenario: Contribution share

- **WHEN** company gross sales are greater than zero
- **THEN** the salesperson's contribution is their sales credit as a percentage of company gross sales

#### Scenario: No company sales recorded

- **WHEN** no orders exist in the period
- **THEN** company gross sales are zero
- **AND** contribution is not presented as a percentage

### Requirement: The member preview stays representative

The system SHALL continue to offer a sample rendering of the salesperson dashboard to administrators and managers, built from fabricated figures and reading nothing from recorded sales.

#### Scenario: Preview renders the full page

- **WHEN** an administrator, operation manager, or project manager opens the member preview
- **THEN** every section of the dashboard renders with sample figures
- **AND** the page states that the figures are sample data
