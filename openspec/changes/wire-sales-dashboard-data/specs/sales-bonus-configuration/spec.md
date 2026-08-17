## Purpose

Governs the financial inputs the sales dashboard depends on: how the company-wide gross target is resolved, and how the product behaves while an administrator has not yet configured a bonus rate and exchange rate.

## ADDED Requirements

### Requirement: Company gross target resolves from an override, then a derivation

The system SHALL use an administrator-set company gross target when one is configured, and otherwise SHALL derive it from the sum of every salesperson's individual target for the period.

#### Scenario: Administrator has set a company target

- **WHEN** a company gross target is configured
- **THEN** that value is used as the company target
- **AND** individual sales targets do not affect it

#### Scenario: No company target configured

- **WHEN** no company gross target is configured
- **THEN** the company target is the sum of all salespeople's targets for the period

#### Scenario: Neither configured nor derivable

- **WHEN** no company target is set and no salesperson has a target for the period
- **THEN** the company target is zero
- **AND** company achievement is not presented as a percentage

### Requirement: An administrator can set the company gross target

The system SHALL allow an administrator to set and change the company gross target, and SHALL NOT allow non-administrators to do so.

#### Scenario: Administrator sets the target

- **WHEN** an administrator saves a company gross target
- **THEN** the value is stored and used on subsequent dashboard views

#### Scenario: Non-administrator attempts to set it

- **WHEN** a user without the administrator role attempts to set the company gross target
- **THEN** the request is rejected

### Requirement: Unconfigured bonus rates are stated, not disguised

While no bonus configuration applies to the viewed period, the system SHALL present the resulting zero bonus together with an explanation that rates have not been configured.

#### Scenario: No bonus configuration exists

- **WHEN** a salesperson views the dashboard and no bonus configuration applies to the period
- **THEN** the bonus amount is shown as zero
- **AND** a note states that an administrator has not configured the bonus rates yet
- **AND** the note states that the bonus will apply once configured

#### Scenario: Sales figures remain live while unconfigured

- **WHEN** bonus rates are unconfigured
- **THEN** sales, target, achievement, and extra sales figures are still calculated and shown

#### Scenario: Configuration becomes available

- **WHEN** an administrator configures a bonus rate and exchange rate covering the period
- **THEN** the dashboard shows the calculated bonus
- **AND** the unconfigured note is no longer shown

### Requirement: Bonus rate is stored as a fraction and shown as a percentage

The system SHALL store the bonus rate as a decimal fraction and SHALL present it to salespeople as a percentage.

#### Scenario: Rate presentation

- **WHEN** a bonus rate of 0.01 is configured
- **THEN** the dashboard presents the rate as 1%
