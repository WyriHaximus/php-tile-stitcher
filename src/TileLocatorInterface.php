<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

interface TileLocatorInterface
{
    /** @return iterable<Tile> */
    public function locate(): iterable;
}
