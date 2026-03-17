<?php

namespace MrEduar\S3M\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class AllowedFolder implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (config('s3m.allow_change_folder') === false && $value !== config('s3m.default_folder', 'tmp')) {
            $fail(__('You are not allowed to change the :attribute of the uploaded file.'));
        }
    }
}
