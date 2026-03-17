<?php

namespace MrEduar\S3M\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedBucket implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('s3m.allow_change_bucket') === false && ! empty($value)) {
            $fail(__('You are not allowed to change the :attribute of the uploaded file.'));
        }
    }
}
