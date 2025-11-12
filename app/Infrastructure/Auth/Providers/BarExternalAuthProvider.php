<?php

namespace App\Infrastructure\Auth\Providers;

use App\Domain\Auth\Contracts\ExternalAuthProvider;
use App\Domain\Common\VendorSystem;
use External\Bar\Auth\LoginService;

final readonly class BarExternalAuthProvider implements ExternalAuthProvider
{
    public function __construct(private LoginService $client) {}

    public function authenticate(string $login, string $password): bool
    {
        // (comment: adjust to external method name)
        return (bool) $this->client->login($login, $password);
    }

    public function system(): VendorSystem { return VendorSystem::BAR; }
}
