# Multipart Uploads using Laravel and AWS S3

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mreduar/laravel-s3-multipart.svg?style=flat-square)](https://packagist.org/packages/mreduar/laravel-s3-multipart)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mreduar/laravel-s3-multipart/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mreduar/laravel-s3-multipart/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mreduar/laravel-s3-multipart/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mreduar/laravel-s3-multipart/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mreduar/laravel-s3-multipart.svg?style=flat-square)](https://packagist.org/packages/mreduar/laravel-s3-multipart)

Upload large files directly to AWS S3 using Laravel.

Sometimes when running an application in a serverless environment, you may not store files permanently on the local filesystem, since you can never be sure that the same serverless "container" will be used on a subsequent request. All files should be stored in a cloud storage system, such as AWS S3, or in a shared file system through AWS EFS.

When uploading large files to S3, you may run into the 5GB limit for a single PUT request. This package allows you to upload large files to S3 by splitting the file into smaller parts and uploading them in parallel.

## Features

-   Upload large files that exceed the 5GB limit
-   Upload files in parallel
-   Configurable Chunked uploads
-   Configurable number of parallel uploads

## Installation

You can install the package via composer:

```bash
composer require mreduar/laravel-s3-multipart
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
