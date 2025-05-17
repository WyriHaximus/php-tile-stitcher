<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use RuntimeException;
use WyriHaximus\TileStitcher\CallalbleTileLocator;
use WyriHaximus\TileStitcher\Coordinate;
use WyriHaximus\TileStitcher\FileLoader;
use WyriHaximus\TileStitcher\Tile;
use WyriHaximus\TileStitcher\TileLocatorInterface;

use function array_map;
use function basename;
use function count;
use function dir;
use function explode;
use function is_file;
use function Safe\getimagesize;
use function strlen;
use function strpos;
use function substr;

final class Provider
{
    /** @return iterable<string, array<int|string|Tile|TileLocatorInterface>> */
    public static function tiles(): iterable
    {
        $convertPathToTile = static fn (string $fileName): Tile => new Tile(
            new Coordinate(
                ...array_map(
                    'intval',
                    explode(
                        'x',
                        substr(
                            basename($fileName),
                            0,
                            (strpos(basename($fileName), '.') !== false ? strpos(basename($fileName), '.') : strlen(basename($fileName))),
                        ),
                    ),
                ),
            ),
            new FileLoader($fileName),
        );

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

                $tileImages[] = $convertPathToTile($images->path . $image);
            }

            $images->close();

            $mapSize = getimagesize(__DIR__ . '/maps/' . $tile . '.png');
            if ($mapSize === null) {
                throw new RuntimeException('Unable to get tile\'s size: ' . $tile);
            }

            yield $tile . ' tile list' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...$tileImages,
            ];

            yield $tile . ' tile loader' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                new CallalbleTileLocator(
                    $tiles->path . $tile . '/',
                    $convertPathToTile,
                ),
            ];

            if (count($tileImages) <= 1) {
                continue;
            }

            yield $tile . ' mixed tile list with a tile loader' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                new CallalbleTileLocator(
                    $tiles->path . $tile . '/',
                    $convertPathToTile,
                ),
                ...$tileImages,
            ];
        }

        $tiles->close();
    }
}
