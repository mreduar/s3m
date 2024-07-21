<?php

namespace MrEduar\LaravelS3Multipart\Contracts;

use Illuminate\Http\JsonResponse;
use MrEduar\LaravelS3Multipart\Http\Requests\CompleteMultipartUploadRequest;
use MrEduar\LaravelS3Multipart\Http\Requests\CreateMultipartUploadRequest;
use MrEduar\LaravelS3Multipart\Http\Requests\SignPartRequest;

interface StorageMultipartUploadControllerContract
{
    public function createMultipartUpload(CreateMultipartUploadRequest $request): JsonResponse;

    public function signPartUpload(SignPartRequest $request): JsonResponse;

    public function completeMultipartUpload(CompleteMultipartUploadRequest $request): JsonResponse;
}
