<?php

namespace App\Services\Banking;

use Illuminate\Support\Str;

class AccountNumberGenerator
{
    /**
     * Format: YYYY-XXXXXXX (Year + 7 random digits)
     */
    public static function make(): string
    {
        $year = now()->format('Y');
        $random = str_pad((string) random_int(0, 9999999), 7, '0', STR_PAD_LEFT);
        return "{$year}-{$random}";
    }
}
