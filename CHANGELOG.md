# Changelog

All notable changes to `s3m` will be documented in this file.

## v2.0.0 - 2026-03-17

### Breaking

- Dropped Laravel 10 support. Minimum requirement is now Laravel 11. PHP 8.2+ required.

### Security

- Error responses no longer expose internal exception messages — generic messages are returned to the client while exceptions are logged via `report()`.

### Features

- Configurable signed URL expiration via `s3m.signed_url_expiration` config key.
- Configurable default visibility via `s3m.default_visibility` config key.
- Configurable default folder via `s3m.default_folder` config key.
- Added Laravel 12 and PHP 8.4 support.

### Refactoring

- Replaced inline closure-based validation with reusable `AllowedBucket`, `AllowedVisibility`, and `AllowedFolder` validation rules.
- Removed direct `$_ENV['AWS_BUCKET']` access in favor of `S3M::getBucket()`.
- Added return types to `SignPartRequest` and `CompleteMultipartUploadRequest`.
- Added `RequestInterface` type hint to `headers()` method.

### Build

- Bumped dev dependencies to latest major versions (Pest 3, Larastan 3, Testbench 10).
- Updated CI matrix for Laravel 11/12 with PHP 8.2–8.4.

**Full Changelog**: https://github.com/mreduar/s3m/compare/v1.3.1...v2.0.0

## v1.3.1 - 2025-05-21

### Changes

- Added extra assertions to tests. @mreduar (#12)
- Fix typos in S3MultipartControllerTest descriptions @mreduar (#11)
- Fix S3M facade annotation @mreduar (#9)
- Fix startUpload call @mreduar (#10)
- Bump dependabot/fetch-metadata from 2.3.0 to 2.4.0 @[dependabot[bot]](https://github.com/apps/dependabot) (#7)

## v1.3.0 - 2025-04-18

### Changes

- Make things better <3 @aaronaccessvr (#6)
- Bump aglipanci/laravel-pint-action from 2.4 to 2.5 @[dependabot[bot]](https://github.com/apps/dependabot) (#5)
- Bump dependabot/fetch-metadata from 2.2.0 to 2.3.0 @[dependabot[bot]](https://github.com/apps/dependabot) (#4)

## v1.2.0 - 2024-11-12

### Changes

* Now its possible to change visibility and folder of the uploaded files

## v1.1.1 - 2024-11-12

### Changes

* Created S3M Facade and Class to reuse logic if neccesary and make optional the complete multipart upload

## v1.1.0 - 2024-07-22

### Changes

* Implemented retry functionality

## v1.0.0 - 2024-07-22

### Changes

First Release
