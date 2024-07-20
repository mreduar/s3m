<?php

namespace MrEduar\LaravelS3Multipart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \MrEduar\LaravelS3Multipart\LaravelS3Multipart
 */
class LaravelS3Multipart extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MrEduar\LaravelS3Multipart\LaravelS3Multipart::class;
    }
}
