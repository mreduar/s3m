# Multipart Uploads using Laravel and AWS S3

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mreduar/laravel-s3-multipart.svg?style=flat-square)](https://packagist.org/packages/mreduar/laravel-s3-multipart)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mreduar/laravel-s3-multipart/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mreduar/laravel-s3-multipart/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mreduar/laravel-s3-multipart/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mreduar/laravel-s3-multipart/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mreduar/laravel-s3-multipart.svg?style=flat-square)](https://packagist.org/packages/mreduar/laravel-s3-multipart)

Upload large files directly to AWS S3 using Laravel

## Installation

You can install the package via composer:

```bash
composer require mreduar/laravel-s3-multipart
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="laravel-s3-multipart-config"
```

This is the contents of the published config file:

```php
return [
];
```

## Usage

```php
//
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

-   [Eduar Bastidas](https://github.com/mreduar)
-   [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
