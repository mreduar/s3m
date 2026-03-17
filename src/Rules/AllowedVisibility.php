<?php

namespace MrEduar\S3M\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedVisibility implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('s3m.allow_change_visibility') === false && $value !== config('s3m.default_visibility', 'private')) {
            $fail(__('You are not allowed to change the :attribute of the uploaded file.'));
        }
    }
}
