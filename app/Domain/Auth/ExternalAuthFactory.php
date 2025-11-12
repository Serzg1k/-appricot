<?php

namespace App\Domain\Auth;

use App\Domain\Auth\Contracts\ExternalAuthProvider;
use App\Domain\Common\VendorSystem;
use App\Infrastructure\Auth\Providers\BarExternalAuthProvider;
use App\Infrastructure\Auth\Providers\BazExternalAuthProvider;
use App\Infrastructure\Auth\Providers\FooExternalAuthProvider;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Contracts\Container\Container;

final readonly class ExternalAuthFactory
{
    public function __construct(
        private Container      $container,
        private PrefixDetector $detector
    ) {}

    /**
     * Resolve provider by login (or dev local).
     * @throws BindingResolutionException
     */
    public function forLogin(string $login): ?ExternalAuthProvider
    {
        return match ($this->detector->detect($login)) {
            VendorSystem::FOO => $this->container->make(FooExternalAuthProvider::class),
            VendorSystem::BAR => $this->container->make(BarExternalAuthProvider::class),
            VendorSystem::BAZ => $this->container->make(BazExternalAuthProvider::class),
            default => null,
        };
    }
}
