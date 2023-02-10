# Installation

## Symfony

See [Symfony Frontend](https://docs.strata.dev/symfony-frontend/v/release%2F0.8.0/installation) for how to install Strata 
Frontend within an existing Symfony application.

## Standalone

Install via Composer:

```
composer require strata/frontend:^0.8
```

### During development of 0.8 branch

Add to your `composer.json`:

```
{
  "minimum-stability": "dev",
  "prefer-stable": true
}
```

Then run this Composer command:
```
composer require "strata/frontend:dev-release/0.8.0 as 0.8.0"
```
