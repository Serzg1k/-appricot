<?php

namespace App\Services\Movies;

use App\Domain\Common\VendorSystem;
use App\Domain\Movies\Contracts\MovieAggregator as MovieAggregatorContract;
use App\Domain\Movies\Contracts\MovieProvider;

final readonly class MovieAggregator implements MovieAggregatorContract
{
    /** @param iterable<MovieProvider> $providers */
    public function __construct(private iterable $providers) {}

    public function getAllTitles(?VendorSystem $context = null, bool $refresh = false): array
    {
        $filtered = [];
        foreach ($this->providers as $p) {
            if ($context === null || $p->system() === $context) {
                $filtered[] = $p;
            }
        }

        $all = [];
        $ok  = false;

        foreach ($filtered as $p) {
            $titles = $p->titles($refresh);
            if (!empty($titles)) { $ok = true; }
            $all = array_merge($all, $titles);
        }

        $all = array_values(array_unique(array_filter($all, fn($s) => $s !== '')));
        return ['titles' => $all, 'atLeastOneOk' => $ok];
    }
}
