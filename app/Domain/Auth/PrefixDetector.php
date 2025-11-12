<?php

namespace App\Domain\Auth;

use App\Domain\Common\VendorSystem;

final class PrefixDetector
{
    /**
     * @param string $login
     * @return VendorSystem|null
     */
    public function detect(string $login): ?VendorSystem
    {
        $rx = self::loginRegex();
        if (!preg_match($rx, $login, $m)) {
            return null;
        }
        return VendorSystem::tryFromInsensitive($m[1]);
    }

    public static function loginRegex(): string
    {
        // Collect allowed vendor strings from enum
        $vendors = array_map(fn(VendorSystem $v) => preg_quote($v->value, '/'), VendorSystem::cases());
        $altern  = implode('|', $vendors);
        return '/^(' . $altern . ')_[A-Z0-9_]+$/';
    }
}
