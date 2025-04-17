<?php

namespace MrEduar\S3M\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class CompleteMultipartUploadRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return Gate::allows('uploadFiles', [$this->user(), $this->input('bucket')]);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'bucket' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (config('s3m.allow_change_bucket') === false && ! empty($value)) {
                    $fail(__('You are not allowed to change the :attribute of the uploaded file.', [
                        'attribute' => $attribute,
                    ]));
                }
            }],
            'key' => ['required', 'string'],
            'upload_id' => ['required', 'string'],
            'parts' => ['required', 'array'],
            'parts.*.PartNumber' => ['required', 'integer'],
            'parts.*.ETag' => ['required', 'string'],
        ];
    }
}
