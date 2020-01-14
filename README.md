# Frontend

Framework to build an efficient front-end website with content from a Headless CMS or other data sources.

**Important note:** As of release 0.7.0 this repo will move to [strata/frontend](https://github.com/strata/frontend) and the old package studio24/frontend will be marked as "Abandoned" in Composer, with a clear link to the new project. We will also be updating minimum requirements to PHP 7.2 and Symfony 5.

[![Build Status](https://travis-ci.org/studio24/frontend.svg?branch=master)](https://travis-ci.org/studio24/frontend) 
[![version][version-badge]][CHANGELOG] [![license][license-badge]][LICENSE]

[version-badge]: https://img.shields.io/badge/version-0.6.5-blue.svg

## Status

Please note this is development software, usage and the project name may change before the 1.0 release. 
See the [roadmap](ROADMAP.md) for more details on future development plans. 

## Requirements

* PHP 7.1+
* Composer

## Installation

```
composer require studio24/frontend:0.6.*
```

## Documentation

See [docs](docs/index.md) or via the GitHub pages site at: [https://studio24.github.io/frontend/](https://studio24.github.io/frontend/)

Docs are published to GitHub Pages via [Jekyll](https://jekyllrb.com/docs/pages/) which uses [Kramdown](https://kramdown.gettalong.org/parser/html.html) 
to parse markdown to HTML and the [Liquid templating](https://jekyllrb.com/docs/liquid/) 
language. Liquid uses a similar syntax to Twig, so if you need to include Twig tags in your docs files ensure you wrap your 
page content in `raw` Liquid tags to avoid errors. For example:  

```
{% raw %}

Your Markdown here

{% if page.content.field is not empty %}
    Do something
{% endif %}

{% endraw %}
```

## Tests

Run [PHPUnit](https://phpunit.readthedocs.io/en/8.0/) tests via: 

```
vendor/bin/phpunit
```

### PHP CodeSniffer

You can test coding standards (PSR2) via:

```
# Summary report
vendor/bin/phpcs --report=summary

# Full details
vendor/bin/phpcs
```

Where possible you can auto-fix code via:

```
vendor/bin/phpcbf
```

## Contributions

Please do contribute! Issues and pull requests are welcome.

Please note [Travis CI](https://travis-ci.org/studio24/frontend) is setup to run PHP linting, PHPUnit and PHP CodeSniffer 
on all merges into master.

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

[CHANGELOG]: ./CHANGELOG.md
[LICENSE]: ./LICENSE
[license-badge]: https://img.shields.io/badge/license-MIT-blue.svg
