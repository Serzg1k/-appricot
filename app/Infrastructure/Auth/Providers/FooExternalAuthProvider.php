<?php

namespace App\Infrastructure\Auth\Providers;

use App\Domain\Auth\Contracts\ExternalAuthProvider;
use App\Domain\Common\VendorSystem;
use External\Foo\Auth\AuthWS;

final readonly class FooExternalAuthProvider implements ExternalAuthProvider
{
    public function __construct(private AuthWS $client) {}

    /**
     * @param string $login
     * @param string $password
     * @return bool
     */
    public function authenticate(string $login, string $password): bool
    {
        try {
            $this->client->authenticate($login, $password);
            return true;
        } catch (\External\Foo\Exceptions\AuthenticationFailedException $e) {
            return false;
        } catch (\Throwable $e) {
            \Log::warning('FOO auth error', ['login' => $login, 'err' => $e->getMessage()]);
            return false;
        }
    }

    public function system(): VendorSystem { return VendorSystem::FOO; }
}
