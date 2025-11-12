<?php

namespace App\Domain\Auth\Contracts;

use App\Domain\Common\VendorSystem;

interface ExternalAuthProvider
{
    /**
     * Authenticate user in external system.
     * Return true on success, false otherwise.
     */
    public function authenticate(string $login, string $password): bool;

    /**
     * System code for diagnostics (e.g. "FOO", "BAR", "BAZ")
     */
    public function system(): VendorSystem;
}
