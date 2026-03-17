<?php

namespace MrEduar\S3M\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use MrEduar\S3M\Rules\AllowedBucket;

class CompleteMultipartUploadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('uploadFiles', [$this->user(), $this->input('bucket')]);
    }

    public function rules(): array
    {
        return [
            'bucket' => ['nullable', 'string', new AllowedBucket],
            'key' => ['required', 'string'],
            'upload_id' => ['required', 'string'],
            'parts' => ['required', 'array'],
            'parts.*.PartNumber' => ['required', 'integer'],
            'parts.*.ETag' => ['required', 'string'],
        ];
    }
}
