<?php

namespace App\Infrastructure\Movies\Providers;

use App\Domain\Common\VendorSystem;
use App\Domain\Movies\Contracts\MovieProvider;
use App\Infrastructure\Movies\Support\NormalizesTitles;
use External\Baz\Exceptions\ServiceUnavailableException;
use External\Baz\Movies\MovieService as BazMovies;

final readonly class BazMovieProvider implements MovieProvider
{
    use NormalizesTitles;
    public function __construct(private BazMovies $client) {}

    public function system(): VendorSystem { return VendorSystem::BAZ; }

    /**
     * @throws ServiceUnavailableException
     */
    public function titles(bool $refresh = false): array
    {
        $raw = $this->client->getTitles();
        return $this->normalizeTitles($raw);
    }
}
