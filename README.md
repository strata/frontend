# Strata Frontend

Deliver fast, accessible user experiences on the modern web.

![Build Status](https://github.com/strata/frontend/workflows/PHP%20tests/badge.svg) 
[![license][license-badge]][LICENSE]

## Status
Please note this software is in development, usage may change before the 1.0 release.

## Requirements

* PHP 7.4+
* [Composer](https://getcomposer.org/)

## Installation

```
composer require strata/frontend:^0.8
```

During development of 0.8 branch:

```
# During dev add this to your composer.json:
"minimum-stability": "dev",
"prefer-stable": true

# Then run this composer command:
composer require "strata/frontend:dev-release/0.8.0 as 0.8.0"
```

## Documentation

See [docs](docs/README.md) or the documentation site at: [https://docs.strata.dev/frontend/](https://docs.strata.dev/frontend/)

## Contributing

Strata is an Open Source project. Find out more about [how to contribute](CONTRIBUTING.md) and our 
[Code of Conduct](CODE_OF_CONDUCT.md).

## Security Issues

If you discover a security vulnerability within Strata, please follow our [disclosure procedure](SECURITY.md).

## About Us

Strata development is sponsored by [Studio 24](https://www.studio24.net/), led by 
[Simon R Jones](https://github.com/simonrjones/).

[CHANGELOG]: ./CHANGELOG.md
[LICENSE]: ./LICENSE
[license-badge]: https://img.shields.io/badge/license-MIT-blue.svg
