<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

interface LoaderInterface
{
    public function load(): string;
}
