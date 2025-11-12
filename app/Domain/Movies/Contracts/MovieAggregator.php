<?php

namespace App\Domain\Movies\Contracts;

use App\Domain\Common\VendorSystem;

/** Aggregates titles from all vendors. */
interface MovieAggregator
{
    /**
     * @param VendorSystem|null $context 'FOO'|'BAR'|'BAZ' or null for all vendors
     * @param bool $refresh bypass provider cache if true
     * @return array{titles:string[], atLeastOneOk:bool}
     */
    public function getAllTitles(?VendorSystem $context = null, bool $refresh = false): array;
}
