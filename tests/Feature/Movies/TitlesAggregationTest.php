<?php

namespace Tests\Feature\Movies;

use Illuminate\Foundation\Testing\WithFaker;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class TitlesAggregationTest extends TestCase
{
    use WithFaker;

    private array $fooTitles;
    private array $barTitles;
    private array $bazTitles;
    protected function setUp(): void
    {
        parent::setUp();

        // (comment) stable random: always same data for this test run
        $this->setUpFaker();
        $this->faker->seed(1234);

        // (comment) generate fake titles once and reuse in fakes + asserts
        $this->fooTitles = [
            $this->faker->sentence(3),
            $this->faker->sentence(4),
            $this->faker->sentence(2),
        ];

        $this->bazTitles = [
            $this->faker->sentence(3),
            $this->faker->sentence(3),
        ];

        $this->barTitles = [
            $this->faker->sentence(3),
            $this->faker->sentence(3),
        ];

        $this->app->instance(\External\Foo\Movies\MovieService::class, new class($this->fooTitles) extends \External\Foo\Movies\MovieService {
            public function __construct(private array $titles) {}
            public function getTitles(): array { return $this->titles; }
        });

        $this->app->instance(\External\Baz\Movies\MovieService::class, new class($this->bazTitles) extends \External\Baz\Movies\MovieService {
            public function __construct(private array $titles) {}
            public function getTitles(): array
            {
                return ['titles' => $this->titles];
            }
        });

        $this->app->instance(\External\Bar\Movies\MovieService::class, new class($this->barTitles) extends \External\Bar\Movies\MovieService {
            public function __construct(private array $titles) {}
            public function getTitles(): array
            {
                return [
                    'titles' => array_map(fn (string $t) => [
                        'title'   => $t,
                        'summary' => 'dummy summary',
                    ], $this->titles),
                ];
            }
        });
    }

    #[Test]
    public function it_aggregates_foo_vendors(): void
    {
        $token = $this->loginAndGetToken('FOO_1', 'foo-bar-baz');

        $this->getJson('/api/movies/titles', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()->assertExactJson($this->fooTitles);
    }

    #[Test]
    public function it_aggregates_bar_vendors(): void
    {
        $token = $this->loginAndGetToken('BAR_1', 'foo-bar-baz');

        $this->getJson('/api/movies/titles', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()->assertExactJson($this->barTitles);
    }

    #[Test]
    public function it_aggregates_baz_vendors(): void
    {
        $token = $this->loginAndGetToken('BAZ_1', 'foo-bar-baz');

        $this->getJson('/api/movies/titles', [
            'Authorization' => "Bearer {$token}",
        ])->assertOk()->assertExactJson($this->bazTitles);
    }

    private function loginAndGetToken(string $login, string $password): string
    {
        $resp = $this->postJson('/api/login', compact('login','password'))->json();
        return $resp['token'];
    }
}
