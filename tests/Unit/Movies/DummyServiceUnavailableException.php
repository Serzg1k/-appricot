<?php

namespace Tests\Unit\Movies;

use App\Domain\Movies\Contracts\MovieProvider;
use App\Domain\Movies\Decorators\ResilientCachedMovieProvider;
use App\Domain\Common\VendorSystem;
use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/** Dummy exception whose class name ends with 'ServiceUnavailableException' */
class DummyServiceUnavailableException extends \RuntimeException {}

class ResilientCachedMovieProviderTest extends TestCase
{
    #[Test]
    public function it_retries_and_caches_success(): void
    {
        Cache::flush();

        $calls = 0;

        $inner = new class(function () use (&$calls) {
            $calls++;
            if ($calls < 2) {
                throw new DummyServiceUnavailableException('temp');
            }
            return ['A','B'];
        }) implements MovieProvider {
            public function __construct(private $fn) {}
            public function system(): VendorSystem { return VendorSystem::FOO; }
            public function titles(bool $refresh = false): array { return ($this->fn)(); }
        };

        $decor = new ResilientCachedMovieProvider($inner, retries: 3, baseSleepMs: 1, ttl: 60);

        // First call: one retry then success
        $out1 = $decor->titles();
        $this->assertSame(['A','B'], $out1);

        // Second call: should be served from cache, no extra inner call
        $callsBefore = $calls;
        $out2 = $decor->titles();
        $this->assertSame(['A','B'], $out2);
        $this->assertSame($callsBefore, $calls);
    }

    #[Test]
    public function it_does_not_cache_failure_and_keeps_old_cache(): void
    {
        Cache::flush();

        // inner returns ok once, then always fails
        $ok = true;
        $inner = new class(function () use (&$ok) {
            if ($ok) { $ok = false; return ['X']; }
            throw new DummyServiceUnavailableException('down');
        }) implements MovieProvider {
            public function __construct(private $fn) {}
            public function system(): VendorSystem { return VendorSystem::BAR; }
            public function titles(bool $refresh = false): array { return ($this->fn)(); }
        };

        $decor = new ResilientCachedMovieProvider($inner, retries: 2, baseSleepMs: 1, ttl: 60);

        $first = $decor->titles();    // caches ['X']
        $this->assertSame(['X'], $first);

        $second = $decor->titles();   // failure → returns cached ['X']
        $this->assertSame(['X'], $second);
    }
}
