<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use RuntimeException;
use WyriHaximus\TileStitcher\Coordinate;
use WyriHaximus\TileStitcher\FileLoader;
use WyriHaximus\TileStitcher\Tile;

use function array_map;
use function dir;
use function explode;
use function is_file;
use function Safe\getimagesize;
use function strlen;
use function strpos;
use function substr;

final class Provider
{
    /** @return iterable<string, array<int|string|Tile>> */
    public static function tiles(): iterable
    {
        $tiles = dir(__DIR__ . '/tiles/');
        if ($tiles === false) {
            throw new RuntimeException('Unable to list relevant tile sets');
        }

        while (($tile = $tiles->read()) !== false) {
            if ($tile === '.' || $tile === '..') {
                continue;
            }

            $tileImages = [];
            $images     = dir($tiles->path . $tile . '/');
            if ($images === false) {
                throw new RuntimeException('Unable to list tiles in tileset: ' . $tile);
            }

            while (($image = $images->read()) !== false) {
                if (! is_file($images->path . $image)) {
                    continue;
                }

                $tileImages[] = new Tile(
                    new Coordinate(
                        ...array_map(
                            'intval',
                            explode(
                                'x',
                                substr(
                                    $image,
                                    0,
                                    (strpos($image, '.') !== false ? strpos($image, '.') : strlen($image)),
                                ),
                            ),
                        ),
                    ),
                    new FileLoader($images->path . $image),
                );
            }

            $images->close();

            $mapSize = getimagesize(__DIR__ . '/maps/' . $tile . '.png');

            yield $tile => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...$tileImages,
            ];
        }

        $tiles->close();
    }
}
