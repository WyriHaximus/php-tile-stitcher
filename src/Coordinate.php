<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

final readonly class Coordinate
{
    public function __construct(
        public int $x,
        public int $y,
    ) {
    }
}
