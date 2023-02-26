# Installation

You can install Strata Frontend via [Composer](https://getcomposer.org/). You can either install to a 
Symfony project or into your PHP project.

Support for Symfony 6 and Laravel is under development.

## Requirements

* PHP 7.4+
* [Composer](https://getcomposer.org/)

## Symfony

Install into your Symfony 5 project:

```
composer require strata/symfony-frontend:^0.8
```

### Twig helpers

Register these with your `config/services.yaml`:

```yaml
# Register Frontend Twig helpers
Strata\Symfony\TwigExtension:
    tags: ['twig.extension']
```


## Standalone

Install into your PHP project:

```
composer require strata/frontend:^0.8
```
