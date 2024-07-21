<?php

namespace MrEduar\LaravelS3Multipart\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class SignPartRequest extends FormRequest
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
            'key' => ['required', 'string'],
            'part_number' => ['required', 'integer'],
            'upload_id' => ['required', 'string'],
            'bucket' => ['nullable', 'string'],
            'visibility' => ['nullable', 'string'],
            'content_type' => ['nullable', 'string'],
        ];
    }
}
