![Multipart Uploads using Laravel and AWS S3](https://raw.githubusercontent.com/mreduar/s3m/main/s3m-banner.png)

# S3M - Multipart Uploads using Laravel and AWS S3

[![Latest Version on Packagist](https://img.shields.io/packagist/v/mreduar/s3m.svg?style=flat-square)](https://packagist.org/packages/mreduar/s3m)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/mreduar/s3m/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/mreduar/s3m/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/mreduar/s3m/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/mreduar/s3m/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/mreduar/s3m.svg?style=flat-square)](https://packagist.org/packages/mreduar/s3m)

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
composer require mreduar/s3m
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
