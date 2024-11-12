<?php

namespace MrEduar\S3M;

use Aws\S3\S3Client;
use Illuminate\Support\Arr;
use InvalidArgumentException;

class S3M
{
    /**
     * Ensure the required environment variables are available.
     *
     * @throws \InvalidArgumentException
     */
    public static function ensureEnvironmentVariablesAreAvailable(?array $options = []): void
    {
        $missing = array_diff_key(array_flip(array_filter([
            Arr::get($options, 'bucket') ? null : 'AWS_BUCKET',
            'AWS_DEFAULT_REGION',
            'AWS_ACCESS_KEY_ID',
            'AWS_SECRET_ACCESS_KEY',
        ])), $_ENV);

        if (empty($missing)) {
            return;
        }

        throw new InvalidArgumentException(
            'Unable to issue signed URL. Missing environment variables: '.implode(', ', array_keys($missing))
        );
    }

    /**
     * Get the S3 storage client instance.
     */
    public function storageClient(): S3Client
    {
        $config = [
            'region' => config('filesystems.disks.s3.region', $_ENV['AWS_DEFAULT_REGION']),
            'version' => 'latest',
            'signature_version' => 'v4',
            'use_path_style_endpoint' => config('filesystems.disks.s3.use_path_style_endpoint', false),
        ];

        $config['credentials'] = array_filter([
            'key' => $_ENV['AWS_ACCESS_KEY_ID'] ?? null,
            'secret' => $_ENV['AWS_SECRET_ACCESS_KEY'] ?? null,
            'token' => $_ENV['AWS_SESSION_TOKEN'] ?? null,
            'url' => $_ENV['AWS_URL'] ?? null,
            'endpoint' => $_ENV['AWS_URL'] ?? null,
        ]);

        if (array_key_exists('AWS_URL', $_ENV) && ! is_null($_ENV['AWS_URL'])) {
            $config['url'] = $_ENV['AWS_URL'];
            $config['endpoint'] = $_ENV['AWS_URL'];
        }

        return new S3Client($config);
    }

    /**
     * Complete a multipart upload.
     */
    public function completeMultipartUpload(?array $options = []): array
    {
        self::ensureEnvironmentVariablesAreAvailable($options);

        $bucket = Arr::get($options, 'bucket') ?: $_ENV['AWS_BUCKET'];

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
