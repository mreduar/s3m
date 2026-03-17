<?php

namespace MrEduar\S3M\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use MrEduar\S3M\Rules\AllowedBucket;
use MrEduar\S3M\Rules\AllowedFolder;
use MrEduar\S3M\Rules\AllowedVisibility;

class CreateMultipartUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Gate::allows('uploadFiles', [$this->user(), $this->input('bucket')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'bucket' => ['nullable', 'string', new AllowedBucket],
            'visibility' => ['nullable', 'string', new AllowedVisibility],
            'content_type' => ['nullable', 'string'],
            'folder' => ['nullable', 'string', new AllowedFolder],
        ];
    }
}
