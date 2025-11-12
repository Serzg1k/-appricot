<?php

namespace App\Domain\Movies\Contracts;

use App\Domain\Common\VendorSystem;

/** Provides movie titles from a single vendor, normalized to string[]. */
interface MovieProvider
{
    /** 'FOO' | 'BAR' | 'BAZ' */
    public function system(): VendorSystem;

    /** @return string[] Normalized titles or empty array on failure. */
    public function titles(bool $refresh = false): array;
}
