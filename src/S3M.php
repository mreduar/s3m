<?php

namespace MrEduar\S3M;

use Aws\S3\S3Client;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class S3M
{
    public static function getBucket(): string
    {
        return config('s3m.s3.bucket');
    }

    /**
     * Ensure the required config variables are available.
     *
     * @throws \InvalidArgumentException
     */
    public static function ensureConfigureVariablesAreAvailable(?array $options = []): void
    {
        $config = config('s3m.s3') ?? [];

        $missing = array_diff_key(array_flip(array_filter([
            Arr::get($options, 'bucket') ? null : 'bucket',
            'region',
            'key',
            'secret',
        ])), $config);

        if (empty($missing)) {
            return;
        }

        throw new InvalidArgumentException(
            'Unable to issue signed URL. Missing S3M config variables: '.implode(', ', array_keys($missing))
        );
    }

    /**
     * Get the S3 storage client instance.
     */
    public function storageClient(): S3Client
    {
        $config = config('s3m.s3');

        $args = [
            'region' => $config['region'],
            'version' => 'latest',
            'signature_version' => 'v4',
            'use_path_style_endpoint' => $config['use_path_style_endpoint'],
        ];

        $args['credentials'] = array_filter([
            'key' => $config['key'] ?? null,
            'secret' => $config['secret'] ?? null,
            'token' => $config['token'] ?? null,
            'url' => $config['url'] ?? null,
            'endpoint' => $config['endpoint'] ?? null,
        ]);

        if (! empty($config['url'])) {
            $args['url'] = $config['url'];
            $args['endpoint'] = $config['endpoint'] ?? null;
        }

        return new S3Client($args);
    }

    /**
     * Complete a multipart upload.
     */
    public function completeMultipartUpload(?array $options = []): array
    {
        self::ensureConfigureVariablesAreAvailable($options);

        $bucket = Arr::get($options, 'bucket') ?: self::getBucket();

        $client = self::storageClient();

        return $client->completeMultipartUpload([
            'Bucket' => $bucket,
            'Key' => Arr::get($options, 'key'),
            'UploadId' => Arr::get($options, 'upload_id'),
            'MultipartUpload' => [
                'Parts' => Arr::get($options, 'parts'),
            ],
        ])->toArray();
    }
}
