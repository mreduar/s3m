<?php

namespace MrEduar\S3M\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static void ensureEnvironmentVariablesAreAvailable(array $options)
 * @method static \Aws\S3\S3Client storageClient()
 * @method static array completeMultipartUpload(array $options = [])
 * @method static string getBucket()
 *
 * @see \MrEduar\S3M\S3M
 */
class S3M extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \MrEduar\S3M\S3M::class;
    }
}
