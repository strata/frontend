# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [0.9.0](https://github.com/strata/frontend/compare/v0.8.3...v0.9.0) (2024-06-24)


### Features

* Update to PHP 8.1+
* Support Symfony 6 and 7

## [0.8.0] - 2023-02-10

### Added
- Added support for GraphQL and CraftCMS
- Added CLI command for code generation

### Changed
- Requires PHP 7.4+
- Replaced Api providers with new flexible data layer powered by [Strata Data](https://github.com/strata/data/). E.g. 
  `Strata\Frontend\Api\Providers\RestApi` is now replaced with `Strata\Data\Http\Rest`
- Moved [documentation](https://docs.strata.dev/frontend/) to GitBook
- Moved continuous integration to use GitHub Actions
- [Twig helpers](docs/templating/twig.md) changed from functions to filters: slugify, fix_url (now defaults to https)
- [Twig helpers](docs/templating/twig.md) changed from functions to tests: is_prod
- See [upgrade guide from 0.6 and 0.7](UPGRADE-PRE-1.0.md#upgrading-from-v0.6-and-0.7-to-v0.8)

## [0.7.0] - 2021-01-06

### Added
- Expand documentation

### Changed
- Upgrade to Symfony 5
- Clean up project folders 
- Update coding standard to PSR12
- Moved composer package to strata namespace
- Switched default branch to main

## [0.6.8] - 2020-03-11
### Fixed
- Fix issue #84 where WordPress only uses the last part of a URL slug to match pages in WordPress. Frontend now validates 
the full page URL when returning content.

## [0.6.7] - 2020-02-28
### Fixed
- Fix issue #82 with WordPress returning multiple page when get page by slug

## [0.6.6] - 2020-02-13
### Added
- Add excerpt and build_version Twig filters
- Add not_empty, all_not_empty, is_prod, staging_banner Twig functions
- Expand Twig documentation

### Fixed
- Fix issue with relation not picking up post type in API response

## [0.6.0] - 2019-07-21
### Added
- Update to Symfony 4.3 
- Expanded content types available (number, decimal, relation_array, array, taxonomyterms)
- Flexible content
- fix_url() Twig function

## [0.5.0] - 2019-03-27
### Added

- Core frontend system
- WordPress API integration
- Basic REST API integration
- Content modelling
- Twig templating
- Caching support
- Front-end templating documentation
