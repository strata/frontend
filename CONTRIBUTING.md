# How to contribute

We welcome contributions to Strata. 

All contributors must agree to our [Code of Conduct](CODE_OF_CONDUCT.md). 

Strata is released under the MIT license and is copyright [Studio 24 Ltd](https://www.studio24.net/). All contributors 
must accept these license and copyright conditions.

## Pull Requests

All contributions must be made on a branch and must pass [unit tests](#tests) and [coding standards](#coding-standards). 

Please create a Pull Request to merge changes into the `main` branch, these will be automatically tested by 
GitHub Actions. 

All Pull Requests need at least one approval from the Studio 24 development team.

## Release

We follow [semantic versioning](https://semver.org/). This can be summarised as:

* MAJOR version when you make incompatible API changes (e.g. 2.0.0)
* MINOR version when you add functionality in a backwards compatible manner (e.g. 2.1.0)
* PATCH version when you make backwards compatible bug fixes (e.g. 2.1.1)
 
During pre-1.0 release a MINOR version can include backwards incompatible API changes. Please ensure these are 
documented in `UPGRADE-PRE-1.0.md`.

Once version 1.0 is reached any upgrade notes should be added to `UPGRADE.md`.
 
### Creating a release
 
To create a new release do the following:

1. Update [CHANGELOG.md](https://github.com/strata/frontend/blob/master/CHANGELOG.md) with a summary of the changes.
1. Create a [new release](https://help.github.com/en/github/administering-a-repository/managing-releases-in-a-repository) 
at GitHub. This will automatically create a new release at [Packagist](https://packagist.org/packages/strata/frontend) 
so code can be loaded via Composer.  

## Tests

You can run all tests (phplint, phpcs, phpunit) from one command via:

```
composer test
```

## Unit tests

Please add unit tests for all bug fixes and new code changes.

Run [PHPUnit](https://phpunit.readthedocs.io/en/8.0/) tests via: 

```
vendor/bin/phpunit
```

## PHPStan

You can use [PHPStan](https://phpstan.org/) to help test code quality, this can help catch simple errors:

```
vendor/bin/phpstan analyse
```

## Coding standards

Strata follows the [PSR-12](https://www.php-fig.org/psr/psr-12/) coding standard. You can check this with:

```
vendor/bin/phpcs
```

Where possible you can auto-fix code via:

```
vendor/bin/phpcbf
```

Please ensure you declare strict types at the top of each PHP file:

```php
declare(strict_types=1);
```

## Documentation

See [docs](docs/README.md) or via the GitHub pages site at: [https://docs.strata.dev/frontend](https://docs.strata.dev/)

Docs are published to GitBook using [markdown](https://docs.gitbook.com/editing-content/markdown).  
