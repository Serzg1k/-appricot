<?php

namespace App\Infrastructure\Movies\Providers;

use App\Domain\Common\VendorSystem;
use App\Domain\Movies\Contracts\MovieProvider;
use App\Infrastructure\Movies\Support\NormalizesTitles;
use External\Bar\Exceptions\ServiceUnavailableException;
use External\Bar\Movies\MovieService as BarMovies;

final readonly class BarMovieProvider implements MovieProvider
{
    use NormalizesTitles;
    public function __construct(private BarMovies $client) {}

    public function system(): VendorSystem { return VendorSystem::BAR; }

    /**
     * @throws ServiceUnavailableException
     */
    public function titles(bool $refresh = false): array
    {
        $raw = $this->client->getTitles();
        return $this->normalizeTitles($raw);
    }
}
