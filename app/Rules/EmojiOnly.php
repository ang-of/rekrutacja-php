<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class EmojiOnly implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/^[\x{1F300}-\x{1F9FF}\x{2600}-\x{26FF}\x{2700}-\x{27BF}\x{1F000}-\x{1F02F}\x{1F0A0}-\x{1F0FF}\x{1F100}-\x{1F64F}\x{1F680}-\x{1F6FF}\x{1F900}-\x{1F9FF}\x{1FA00}-\x{1FAFF}]+$/u', $value);
    }

    public function message()
    {
        return 'The :attribute must be a valid emoji.';
    }
}
