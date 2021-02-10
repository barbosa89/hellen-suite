<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Carbon;

class MinDate implements Rule
{
    private string $message;

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $date = Carbon::parse($value);
        $minDate = now()->subYear();

        if ($date->greaterThanOrEqualTo($minDate)) {
            return true;
        }

        $this->message = trans('validation.after_or_equal', ['attribute' => $attribute, 'date' => $minDate->format('Y-m-d')]);

        return false;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return $this->message;
    }
}
