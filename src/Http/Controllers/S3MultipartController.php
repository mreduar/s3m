<?php

namespace MrEduar\S3M\Http\Controllers;

use Aws\CommandInterface;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use InvalidArgumentException;
use MrEduar\S3M\Contracts\StorageMultipartUploadControllerContract;
use MrEduar\S3M\Http\Requests\CompleteMultipartUploadRequest;
use MrEduar\S3M\Http\Requests\CreateMultipartUploadRequest;
use MrEduar\S3M\Http\Requests\SignPartRequest;

class S3MultipartController extends Controller implements StorageMultipartUploadControllerContract
{
    /**
     * Create a new multipart upload.
     */
    public function createMultipartUpload(CreateMultipartUploadRequest $request): JsonResponse
    {
        $this->ensureEnvironmentVariablesAreAvailable($request);

        $client = $this->storageClient();

        $bucket = $request->input('bucket') ?: $_ENV['AWS_BUCKET'];

        $uuid = (string) Str::uuid();

        $key = $this->getKey($uuid);

        try {
            $uploader = $client->createMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $key,
                'ACL' => $request->input('visibility') ?: $this->defaultVisibility(),
                'ContentType' => $request->input('content_type') ?: 'application/octet-stream',
            ]);

            return response()->json([
                'uuid' => $uuid,
                'bucket' => $bucket,
                'key' => $key,
                'uploadId' => $uploader['UploadId'],
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Sign a part upload.
     */
    public function signPartUpload(SignPartRequest $request): JsonResponse
    {
        $this->ensureEnvironmentVariablesAreAvailable($request);

        $client = $this->storageClient();

        $bucket = $request->input('bucket') ?: $_ENV['AWS_BUCKET'];

        $expiresAfter = 5;

        try {
            $signedRequest = $client->createPresignedRequest(
                $this->createCommand($request, $client, $bucket),
                sprintf('+%s minutes', $expiresAfter)
            );

            $uri = $signedRequest->getUri();

            return response()->json([
                'bucket' => $bucket,
                'key' => $request->input('key'),
                'url' => $uri->getScheme().'://'.$uri->getAuthority().$uri->getPath().'?'.$uri->getQuery(),
                'headers' => $this->headers($request, $signedRequest),
            ], 201);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Complete a multipart upload.
     */
    public function completeMultipartUpload(CompleteMultipartUploadRequest $request): JsonResponse
    {
        $this->ensureEnvironmentVariablesAreAvailable($request);

        $bucket = $request->input('bucket') ?: $_ENV['AWS_BUCKET'];

        $client = $this->storageClient();

        try {
            $completeUpload = $client->completeMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $request->input('key'),
                'UploadId' => $request->input('upload_id'),
                'MultipartUpload' => [
                    'Parts' => $request->input('parts'),
                ],
            ]);

            return response()->json([
                'url' => $completeUpload['Location'],
                'bucket' => $bucket,
                'key' => $request->input('key'),
            ]);
        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Create a command for the PUT operation.
     */
    protected function createCommand(Request $request, S3Client $client, string $bucket): CommandInterface
    {
        return $client->getCommand('UploadPart', array_filter([
            'Bucket' => $bucket,
            'Key' => $request->input('key'),
            'UploadId' => $request->input('upload_id'),
            'PartNumber' => $request->input('part_number'),
            'ACL' => $request->input('visibility') ?: $this->defaultVisibility(),
            'ContentType' => $request->input('content_type') ?: 'application/octet-stream',
        ]));
    }

    /**
     * Get the headers that should be used when making the signed request.
     */
    protected function headers(Request $request, $signedRequest): array
    {
        return array_merge(
            $signedRequest->getHeaders(),
            [
                'Content-Type' => $request->input('content_type') ?: 'application/octet-stream',
            ]
        );
    }

    /**
     * Ensure the required environment variables are available.
     *
     * @throws \InvalidArgumentException
     */
    protected function ensureEnvironmentVariablesAreAvailable(Request $request): void
    {
        $missing = array_diff_key(array_flip(array_filter([
            $request->input('bucket') ? null : 'AWS_BUCKET',
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
    protected function storageClient(): S3Client
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
     * Get key for the given UUID.
     */
    protected function getKey(string $uuid): string
    {
        return 'tmp/'.$uuid;
    }

    /**
     * Get the default visibility for uploads.
     */
    protected function defaultVisibility(): string
    {
        return 'private';
    }
}
