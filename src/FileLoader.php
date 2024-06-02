<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use function Safe\file_get_contents;

final readonly class FileLoader implements LoaderInterface
{
    public function __construct(private string $fileName)
    {
    }

    public function load(): string
    {
        return file_get_contents($this->fileName);
    }
}
