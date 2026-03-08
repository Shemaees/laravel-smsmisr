<?php

namespace Ghanem\LaravelSmsmisr\Rules;

use Closure;
use Ghanem\LaravelSmsmisr\PhoneNumber;
use Illuminate\Contracts\Validation\ValidationRule;

class EgyptianPhoneNumber implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!is_string($value) || !PhoneNumber::isValid($value)) {
            $fail('The :attribute must be a valid Egyptian mobile number.');
        }
    }
}
