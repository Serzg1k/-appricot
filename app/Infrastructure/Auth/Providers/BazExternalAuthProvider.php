<?php

namespace App\Infrastructure\Auth\Providers;

use App\Domain\Auth\Contracts\ExternalAuthProvider;
use App\Domain\Common\VendorSystem;
use External\Baz\Auth\Authenticator;

final readonly class BazExternalAuthProvider implements ExternalAuthProvider
{
    public function __construct(private Authenticator $client) {}

    public function authenticate(string $login, string $password): bool
    {
        // (comment: adjust to external method name)
        return (bool) $this->client->auth($login, $password);
    }

    public function system(): VendorSystem { return VendorSystem::BAZ; }
}
