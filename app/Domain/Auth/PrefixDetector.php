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
        if (!preg_match('/^(FOO|BAR|BAZ)_[A-Z0-9_]+$/', $login, $m)) {
            return null;
        }
        return VendorSystem::tryFromInsensitive($m[1]);
    }
}
