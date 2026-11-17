# Austral Website

[![Version](https://img.shields.io/packagist/v/austral/website-bundle?label=version)](https://packagist.org/packages/austral/website-bundle)
[![CI](https://github.com/austral-project/website-bundle/actions/workflows/ci.yml/badge.svg?branch=4.x)](https://github.com/austral-project/website-bundle/actions/workflows/ci.yml)
[![PHP](https://img.shields.io/packagist/dependency-v/austral/website-bundle/php)](https://packagist.org/packages/austral/website-bundle)
[![Symfony](https://img.shields.io/badge/symfony-7.4-black)](https://symfony.com/releases/7.4)
[![License](https://img.shields.io/packagist/l/austral/website-bundle)](LICENSE)

Austral website generate

> This bundle is part of [Austral](https://github.com/austral-project), a modular back office and CMS for Symfony.

## Requirements

- PHP 8.2 or higher
- Symfony 7.4

## Installation

```bash
composer require austral/website-bundle
```

If you do not use Symfony Flex, register the bundle in `config/bundles.php`:

```php
return [
    // ...
    Austral\WebsiteBundle\AustralWebsiteBundle::class => ['all' => true],
];
```

## Configuration

```yaml
# config/packages/austral_website.yaml
austral_website:
    # ...
```

## Usage

<!-- Minimal, concrete example. -->

## Documentation

The full documentation is available at https://austral.dev.

## Versions

| Bundle | Symfony | PHP | Branch |
|--------|---------|-----|---------|
| 4.x    | 7.4     | ≥ 8.2 | `4.x` |
| 3.1    | 5.4 / 6.4 | ≥ 8.0 | `3.1` (maintenance) |

See the [CHANGELOG](CHANGELOG.md) and, to upgrade from 3.1, [UPGRADE-4.0](UPGRADE-4.0.md).

## Contributing

Contributions are welcome: see the [contributing guide](https://github.com/austral-project/.github/blob/master/CONTRIBUTING.md). To report a security issue, follow the [security policy](https://github.com/austral-project/.github/blob/master/SECURITY.md).

## License

MIT, see [LICENSE](LICENSE).

## Credits

Created by [Matthieu Beurel](https://www.mbeurel.com). Sponsored by [Yipikai Studio](https://yipikai.studio).
