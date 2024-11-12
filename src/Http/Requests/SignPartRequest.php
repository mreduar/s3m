<?php

namespace MrEduar\S3M\Http\Requests;

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
            'visibility' => ['nullable', 'string', function ($attribute, $value, $fail) {
                if (config('s3m.allow_change_visibility') === false && $value !== 'private') {
                    $fail(__('You are not allowed to change the :attribute of the uploaded file.', [
                        'attribute' => $attribute
                    ]));
                }
            }],
            'content_type' => ['nullable', 'string'],
        ];
    }
}
