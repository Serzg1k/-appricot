<?php

namespace Tests\Unit\Auth;

use App\Domain\Auth\PrefixDetector;
use App\Domain\Common\VendorSystem;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class PrefixDetectorTest extends TestCase
{
    #[Test]
    public function it_detects_vendor_by_exact_uppercase_prefix(): void
    {
        $d = new PrefixDetector();

        $this->assertEquals(VendorSystem::FOO, $d->detect('FOO_123'));
        $this->assertEquals(VendorSystem::BAR, $d->detect('BAR_ABC'));
        $this->assertEquals(VendorSystem::BAZ, $d->detect('BAZ_3'));
    }

    #[Test]
    public function it_rejects_wrong_case_or_missing_suffix(): void
    {
        $d = new PrefixDetector();

        $this->assertNull($d->detect('Foo_1'));     // wrong case
        $this->assertNull($d->detect('BAZ_'));      // missing suffix
        $this->assertNull($d->detect('ABC_100'));   // unknown vendor
        $this->assertNull($d->detect('FOO'));       // no underscore
    }
}
