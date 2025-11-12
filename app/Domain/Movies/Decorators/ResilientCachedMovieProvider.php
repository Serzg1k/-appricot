<?php

namespace App\Domain\Movies\Decorators;

use App\Domain\Common\VendorSystem;
use App\Domain\Movies\Contracts\MovieProvider;

/** Adds retries on ServiceUnavailableException and caches results. */
final class ResilientCachedMovieProvider implements MovieProvider
{
    public function __construct(
        private MovieProvider $inner,
        private int $retries = 3,
        private int $baseSleepMs = 200,
        private int $ttl = 300,
    ) {}

    public function system(): VendorSystem { return $this->inner->system(); }

    public function titles(bool $refresh = false): array
    {
        $cacheKey = 'movies.titles.' . $this->system()->value;

        if ($refresh) {
            \Cache::forget($this->cacheKey($cacheKey));
        }

        $cached = \Cache::get($this->cacheKey($cacheKey));
        if ($cached !== null && !$refresh) {
            return $cached;
        }

        $result = $this->fetchWithRetries();

        if ($result !== null) {
            \Cache::put($this->cacheKey($cacheKey), $result, $this->ttl);
            return $result;
        }

        return is_array($cached) ? $cached : [];
    }

    /** @return string[]|null null on failure after retries */
    private function fetchWithRetries(): ?array
    {
        $attempt = 0;
        while (true) {
            try {
                $res = $this->inner->titles();
                return is_array($res) ? array_values(array_filter(array_map('strval', $res))) : [];
            } catch (\Throwable $e) {
                $attempt++;
                $isSvcUnavail = \str_ends_with(\get_class($e), 'ServiceUnavailableException');
                if (!$isSvcUnavail || $attempt >= $this->retries) {
                    \Log::warning('MovieProvider error', [
                        'provider' => $this->inner->system(),
                        'class'    => get_class($e),
                        'msg'      => $e->getMessage(),
                        'attempt'  => $attempt,
                    ]);
                    return null;
                }
                \usleep(($this->baseSleepMs * (2 ** ($attempt - 1))) * 1000);
            }
        }
    }

    private function cacheKey(string $base): string
    {
        return $base;
    }
}
