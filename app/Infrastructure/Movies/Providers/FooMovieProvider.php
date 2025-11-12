<?php

namespace App\Infrastructure\Movies\Providers;

use App\Domain\Common\VendorSystem;
use App\Domain\Movies\Contracts\MovieProvider;
use App\Infrastructure\Movies\Support\NormalizesTitles;
use External\Foo\Exceptions\ServiceUnavailableException;
use External\Foo\Movies\MovieService as FooMovies;

final readonly class FooMovieProvider implements MovieProvider
{
    use NormalizesTitles;
    public function __construct(private FooMovies $client) {}

    public function system(): VendorSystem { return VendorSystem::FOO; }

    /**
     * @throws ServiceUnavailableException
     */
    public function titles(bool $refresh = false): array
    {
        // call external and normalize to string[]
        $raw = $this->client->getTitles();
        return $this->normalizeTitles($raw);
    }

}
