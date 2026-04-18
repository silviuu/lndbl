Lendable Interview Test - Fee Calculation
=========================================

## Installation

`composer install`

## Running

```
# Run tests
vendor/bin/phpunit

# Static analysis
composer analyse

# Code style
composer cs-fix
```

## How It Works

Fee breakpoints are defined in `config/fee_structure.json` for two loan terms (12 and 24 months), covering amounts from £1,000 to £20,000. When a loan amount falls between two breakpoints, the fee is determined by **linear interpolation** between the surrounding breakpoints. The result is then rounded up so that `loan amount + fee` is always divisible by 5.

Dependencies are wired via **PHP-DI** (`config/container.php`). The JSON fee structure is loaded once and cached in memory by `JsonFeeStructureRepository`.
