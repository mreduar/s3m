<?php

namespace MrEduar\S3M\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'bucket' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (config('s3m.allow_change_bucket') === false && ! empty($value)) {
                    $fail(__('You are not allowed to change the :attribute of the uploaded file.', [
                        'attribute' => $attribute,
                    ]));
                }
            }],
            'visibility' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (config('s3m.allow_change_visibility') === false && $value !== 'private') {
                    $fail(__('You are not allowed to change the :attribute of the uploaded file.', [
                        'attribute' => $attribute,
                    ]));
                }
            }],
            'content_type' => ['nullable', 'string'],
            'folder' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (config('s3m.allow_change_folder') === false && $value !== 'tmp') {
                    $fail(__('You are not allowed to change the :attribute of the uploaded file.', [
                        'attribute' => $attribute,
                    ]));
                }
            }],
        ];
    }
}
