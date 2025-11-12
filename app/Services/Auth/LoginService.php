<?php

namespace App\Services\Auth;

use App\Domain\Auth\ExternalAuthFactory;
use Firebase\JWT\JWT;
use Illuminate\Contracts\Container\BindingResolutionException;

final readonly class LoginService {
    public function __construct(private ExternalAuthFactory $factory) {}

    /**
     * @throws BindingResolutionException
     */
    public function authenticateAndIssueToken(string $login, string $password): ?string {
        $provider = $this->factory->forLogin($login);
        if (!$provider) return null;

        if (!$provider->authenticate($login, $password)) return null;

        $payload = [
            'login'   => $login,            // (comment: required by task)
            'context' => $provider->system()->value, // (comment: FOO|BAR|BAZ)
            'iat'     => time(),            // (comment: issued at)
        ];

        $secret = (string) config('jwt.secret', 'your-256-bit-secret'); // (comment: HS256 secret)
        return JWT::encode($payload, $secret, 'HS256');
    }
}
