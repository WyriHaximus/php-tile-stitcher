<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

final readonly class Tile
{
    public function __construct(
        public Coordinate $coordinate,
        public string $fileName,
    ) {
    }
}
