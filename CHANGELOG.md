# Changelog

All notable changes to `s3m` will be documented in this file.

## v3.0.0 - 2026-06-14

### Breaking

- Dropped Laravel 11 support. The supported range is now Laravel 12 and 13 (`illuminate/contracts: ^12.0|^13.0`).

  Laravel 11 has reached the end of its security-support window and every 11.x release is affected by CVE-2026-48019 (CRLF injection in the default email rule), with no patched 11.x version available. Rather than disable Composer's security-advisory blocking to keep testing against a permanently vulnerable framework, support is dropped. Users on Laravel 11 should upgrade.

- Dropped PHP 8.2 support. Minimum requirement is now PHP 8.3 (`php: ^8.3`). The test toolchain (Pest 4, required for Laravel 13) needs PHP 8.3+, so 8.2 can no longer be exercised in CI. PHP 8.2 users can stay on the 2.x line.

### Added

- Laravel 13 support.

### Changed

- Upgraded the dev/test toolchain for Laravel 13: Pest 3 → 4, `pest-plugin-laravel` 3 → 4, `pest-plugin-arch` 3 → 4, `larastan` → ^3.10, `collision` → ^8.8, and `orchestra/testbench` now allows ^10.0|^11.0.

### CI

- The test matrix now targets Laravel 12 and 13 on PHP 8.3 and 8.4. `prefer-lowest` resolves to the first non-advisory release of each line (12.60.0 / 13.10.0), so Composer's advisory blocking stays enabled — no bypass needed.

## v2.0.2 - 2026-06-14

### Security

- Bumped `esbuild` to `^0.28.1` to resolve a high-severity Dependabot alert (GHSA-gv7w-rqvm-qjhr): missing binary integrity verification in esbuild's Deno module could allow remote code execution via `NPM_CONFIG_REGISTRY`. esbuild is a build-only dependency, so the published `dist/index.js`, `dist/index.esm.js`, and `dist/function.umd.js` artifacts are unchanged. `npm audit` reports 0 vulnerabilities.

### Maintenance

- Bumped `dependabot/fetch-metadata` GitHub Action from 2.5.0 to 3.1.0 (#22).

## v2.0.1 - 2026-05-05

### Security

- Resolved 7 open Dependabot alerts (5 high, 2 medium) by replacing `microbundle` (unmaintained since 2022) with `esbuild` for the JS build pipeline. Vulnerable transitive dependencies — `rollup`, `svgo`, `minimatch`, `picomatch`, and `serialize-javascript` — are no longer part of the dependency tree.

### Build

- Replaced `microbundle` with `esbuild`. The published `dist/index.js`, `dist/index.esm.js`, and `dist/function.umd.js` artifacts are regenerated and remain functionally equivalent. No public API changes (PHP or JS).
- `package-lock.json` shrinks from ~7,200 to ~370 lines; `npm install` now reports 0 vulnerabilities.

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
