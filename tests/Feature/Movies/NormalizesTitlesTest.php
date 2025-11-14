<?php

namespace Tests\Unit\Movies;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use App\Infrastructure\Movies\Support\NormalizesTitles;

class NormalizesTitlesTest extends TestCase
{
    /** simple harness to call trait method */
    private function normalize($raw): array
    {
        return (new class {
            use NormalizesTitles { normalizeTitles as public run; }
        })->run($raw);
    }

    #[Test]
    public function it_handles_plain_strings_list(): void
    {
        $this->assertSame(['A','B'], $this->normalize(['A','B']));
    }

    #[Test]
    public function it_handles_titles_envelope_with_strings(): void
    {
        $raw = ['titles' => ['X','Y']];
        $this->assertSame(['X','Y'], $this->normalize($raw));
    }

    #[Test]
    public function it_handles_objects_with_title_and_ignores_summary(): void
    {
        $raw = ['titles' => [
            ['title' => 'M', 'summary' => '...'],
            ['title' => 'N'],
        ]];
        $this->assertSame(['M','N'], $this->normalize($raw));
    }
}
