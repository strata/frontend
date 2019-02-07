# Frontend

Framework to build an efficient front-end website with content from a Headless CMS or other data sources.

[![Build Status](https://travis-ci.org/studio24/frontend.svg?branch=master)](https://travis-ci.org/studio24/frontend)

## Status

Please note this is development software, usage and the project name may change before the 1.0 release. 
See the [roadmap](ROADMAP.md) for more details on future development plans. 

## Requirements

* PHP 7.1+
* Composer

## Installation

Until version 0.5 you need to install via dev-master. 

```
composer require studio24/frontend:dev-master
```

## Documentation

See [docs](docs/index.md) or via the GitHub pages site at: [https://studio24.github.io/frontend/](https://studio24.github.io/frontend/)

Docs are published to GitHub Pages via [Jekyll](https://jekyllrb.com/docs/pages/) which uses the [Liquid templating](https://jekyllrb.com/docs/liquid/) 
language. This uses a similar syntax to Twig, so if you need to include Twig tags in your docs files ensure you wrap your 
page content in `raw` Liquid tags. For example:  

```
{% raw %}

Your Markdown here

{% if page.content.field is not empty %}
    Do something
{% endif %}

{% endraw %}
```

## Making changes

Commit to a branch, request a PR to merge into master.

## Tests

Run [PHPUnit](https://phpunit.readthedocs.io/en/8.0/) tests via: 

```
vendor/bin/phpunit
```

## License

The MIT License (MIT). Please see [License File](LICENSE) for more information.

