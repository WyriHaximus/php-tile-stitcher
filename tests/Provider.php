<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use RuntimeException;
use WyriHaximus\TileStitcher\Coordinate;
use WyriHaximus\TileStitcher\Tile;

use function array_map;
use function array_reverse;
use function dir;
use function explode;
use function is_file;
use function Safe\getimagesize;
use function strlen;
use function strpos;
use function substr;
use function usort;

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
                    $images->path . $image,
                );
            }

            $images->close();

            $mapSize = getimagesize(__DIR__ . '/maps/' . $tile . '.png');

            yield $tile . '_unsorted' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...$tileImages,
            ];

            yield $tile . '_reversed' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...array_reverse($tileImages),
            ];

            usort($tileImages, static fn (Tile $left, Tile $right): int => self::parseCoordsFromTileFileName($left->fileName)[0] <=> self::parseCoordsFromTileFileName($right->fileName)[0]);

            yield $tile . '_half_sorted' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...$tileImages,
            ];

            yield $tile . '_half_sorted_reversed' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...array_reverse($tileImages),
            ];

            usort($tileImages, static fn (Tile $left, Tile $right): int => self::parseCoordsFromTileFileName($left->fileName)[1] <=> self::parseCoordsFromTileFileName($right->fileName)[1]);

            yield $tile . '_sorted' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...$tileImages,
            ];

            yield $tile . '_sorted_reversed' => [
                $mapSize[0],
                $mapSize[1],
                __DIR__ . '/maps/' . $tile . '.png',
                ...array_reverse($tileImages),
            ];
        }

        $tiles->close();
    }

    /** @return array<int> */
    private static function parseCoordsFromTileFileName(string $fileName): array
    {
        [$x, $y] = explode('x', substr($fileName, 0, -4));

        return [(int) $x, (int) $y];
    }
}
