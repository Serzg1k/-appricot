<?php

namespace App\Domain\Common;

enum VendorSystem: string
{
    case FOO = 'FOO';
    case BAR = 'BAR';
    case BAZ = 'BAZ';

    /** Create from string (case-insensitive); returns null if unknown. */
    public static function tryFromInsensitive(?string $s): ?self
    {
        if (!$s) return null;
        $u = strtoupper($s);
        return match ($u) {
            'FOO' => self::FOO,
            'BAR' => self::BAR,
            'BAZ' => self::BAZ,
            default => null,
        };
    }
}
