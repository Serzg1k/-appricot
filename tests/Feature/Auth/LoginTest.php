<?php

namespace Tests\Feature\Auth;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;

class LoginTest extends TestCase
{
    #[Test]
    public function it_fails_on_invalid_login_format(): void
    {
        $this->postJson('/api/login', ['login' => 'Foo_123', 'password' => 'x'])
            ->assertStatus(401)
            ->assertExactJson(['status' => 'failure']);
    }

    #[Test]
    public function it_logs_in_with_foo_credentials(): void
    {
        $this->postJson('/api/login', ['login' => 'FOO_1', 'password' => 'foo-bar-baz'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function it_fails_with_wrong_password_for_foo(): void
    {
        $this->postJson('/api/login', ['login' => 'FOO_1', 'password' => 'wrong'])
            ->assertStatus(401)
            ->assertExactJson(['status' => 'failure']);
    }

    #[Test]
    public function it_logs_in_with_bar_credentials(): void
    {
        $this->postJson('/api/login', ['login' => 'BAR_7', 'password' => 'foo-bar-baz'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['token']);
    }

    #[Test]
    public function it_logs_in_with_baz_credentials(): void
    {
        $this->postJson('/api/login', ['login' => 'BAZ_42', 'password' => 'foo-bar-baz'])
            ->assertOk()
            ->assertJsonPath('status', 'success')
            ->assertJsonStructure(['token']);
    }
}
