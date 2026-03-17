<?php

namespace MrEduar\S3M\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;
use MrEduar\S3M\Rules\AllowedVisibility;

class SignPartRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('uploadFiles', [$this->user(), $this->input('bucket')]);
    }

    public function rules(): array
    {
        return [
            'key' => ['required', 'string'],
            'part_number' => ['required', 'integer'],
            'upload_id' => ['required', 'string'],
            'bucket' => ['nullable', 'string'],
            'visibility' => ['nullable', 'string', new AllowedVisibility],
            'content_type' => ['nullable', 'string'],
        ];
    }
}
