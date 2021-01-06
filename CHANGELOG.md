# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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
