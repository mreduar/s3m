<?php

namespace MrEduar\LaravelS3Multipart;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use MrEduar\LaravelS3Multipart\Commands\LaravelS3MultipartCommand;

class LaravelS3MultipartServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-s3-multipart')
            ->hasConfigFile();
    }
}
