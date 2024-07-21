<?php

namespace MrEduar\LaravelS3Multipart\Http\Requests;

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
            'bucket' => ['nullable', 'string'],
            'key' => ['required', 'string'],
            'upload_id' => ['required', 'string'],
            'parts' => ['required', 'array'],
            'parts.*.PartNumber' => ['required', 'integer'],
            'parts.*.ETag' => ['required', 'string'],
        ];
    }
}
