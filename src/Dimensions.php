<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

final readonly class Dimensions
{
    public function __construct(
        public int $width,
        public int $height,
    ) {
    }
}
