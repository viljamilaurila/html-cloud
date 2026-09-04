<?php

namespace App\Enums;

use Illuminate\Support\Carbon;

/**
 * The `expires_in` values the API accepts. Uploads default to a month.
 */
enum Expiry: string
{
    case Week = '7';
    case Month = '30';
    case Never = 'never';

    public function expiresAt(): ?Carbon
    {
        return match ($this) {
            self::Week => now()->addDays(7),
            self::Month => now()->addDays(30),
            self::Never => null,
        };
    }
}
