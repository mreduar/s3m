<?php

namespace MrEduar\S3M\Contracts;

use Illuminate\Http\JsonResponse;
use MrEduar\S3M\Http\Requests\CompleteMultipartUploadRequest;
use MrEduar\S3M\Http\Requests\CreateMultipartUploadRequest;
use MrEduar\S3M\Http\Requests\SignPartRequest;

interface StorageMultipartUploadControllerContract
{
    public function createMultipartUpload(CreateMultipartUploadRequest $request): JsonResponse;

    public function signPartUpload(SignPartRequest $request): JsonResponse;

    public function completeMultipartUpload(CompleteMultipartUploadRequest $request): JsonResponse;
}
