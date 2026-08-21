<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Translation\PotentiallyTranslatedString;

class DueDateAfterStartDate implements ValidationRule
{
    public function __construct(private ?string $startDate = null) {}

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (! $this->startDate || ! $value) {
            return;
        }

        try {
            $due = \Carbon\Carbon::parse($value)->startOfDay();
            $start = \Carbon\Carbon::parse($this->startDate)->startOfDay();

            if ($due->lt($start)) {
                $fail('The due date cannot be earlier than the start date.');
            }
        } catch (\Exception) {
            $fail('The due date is not a valid date.');
        }
    }
}
