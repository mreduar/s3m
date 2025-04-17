<?php

namespace MrEduar\S3M\Http\Controllers;

use Aws\CommandInterface;
use Aws\S3\S3Client;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Str;
use MrEduar\S3M\Contracts\StorageMultipartUploadControllerContract;
use MrEduar\S3M\Events\MultipartUploadCompleted;
use MrEduar\S3M\Events\MultipartUploadCreated;
use MrEduar\S3M\Facades\S3M;
use MrEduar\S3M\Http\Requests\CompleteMultipartUploadRequest;
use MrEduar\S3M\Http\Requests\CreateMultipartUploadRequest;
use MrEduar\S3M\Http\Requests\SignPartRequest;

class S3MultipartController extends Controller implements StorageMultipartUploadControllerContract
{

    public function __construct()
    {
        $this->middleware(config('s3m.middleware'));
    }

    /**
     * Create a new multipart upload.
     */
    public function createMultipartUpload(CreateMultipartUploadRequest $request): JsonResponse
    {
        S3M::ensureConfigureVariablesAreAvailable($request->only('bucket'));

        $client = S3M::storageClient();

        $bucket = $request->input('bucket') ?: $_ENV['AWS_BUCKET'];

        $uuid = (string) Str::uuid();

        $key = $this->getKey($uuid, $request->input('folder', 'tmp'));

        try {
            $uploader = $client->createMultipartUpload([
                'Bucket' => $bucket,
                'Key' => $key,
                'ACL' => $request->input('visibility') ?: $this->defaultVisibility(),
                'ContentType' => $request->input('content_type') ?: 'application/octet-stream',
            ]);

            MultipartUploadCreated::dispatch($uuid, $bucket, $key, $uploader['UploadId']);

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
        S3M::ensureConfigureVariablesAreAvailable($request->only('bucket'));

        $client = S3M::storageClient();

        $bucket = $request->input('bucket') ?: S3M::getBucket();

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
        try {
            $data = S3M::completeMultipartUpload($request->all());

            MultipartUploadCompleted::dispatch(
                $data['Bucket'],
                $data['Key'],
                $request->input('upload_id'),
                $data['Location'],
            );

            return response()->json([
                'url' => $data['Location'],
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
     * Get key for the given UUID.
     */
    protected function getKey(string $uuid, string $folder): string
    {
        return $folder.'/'.$uuid;
    }

    /**
     * Get the default visibility for uploads.
     */
    protected function defaultVisibility(): string
    {
        return 'private';
    }
}
