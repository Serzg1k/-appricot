<?php

namespace Tests\Feature\Movies;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TitlesAuthTest extends TestCase
{
    #[Test]
    public function it_requires_bearer_token(): void
    {
        $this->getJson('/api/movies/titles')
            ->assertStatus(401)
            ->assertExactJson(['status' => 'failure']);
    }

    #[Test]
    public function it_works_with_valid_bearer_token(): void
    {
        $token = $this->loginAndGetToken('FOO_1', 'foo-bar-baz');

        $this->getJson('/api/movies/titles', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()->assertJsonIsArray();
    }

    // --- helpers ---

    /** Login helper to obtain JWT token. */
    private function loginAndGetToken(string $login, string $password): string
    {
        $resp = $this->postJson('/api/login', compact('login','password'))
            ->assertOk()
            ->json();

        return $resp['token'];
    }
}
