<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use WyriHaximus\TestUtilities\TestCase;
use WyriHaximus\TileStitcher\Dimensions;
use WyriHaximus\TileStitcher\Map;
use WyriHaximus\TileStitcher\Tile;
use WyriHaximus\TileStitcher\TileLocatorInterface;

final class MapTest extends TestCase
{
    /**
     * @test
     * @dataProvider \WyriHaximus\Tests\TileStitcher\Provider::tiles
     */
    public function calculateMap(int $expectedWidth, int $expectedHeight, string $expectedOutput, Tile|TileLocatorInterface ...$tiles): void
    {
        $map = Map::calculate(
            new Dimensions(512, 512),
            ...$tiles,
        );
        self::assertSame($expectedWidth, $map->dimensions->width);
        self::assertSame($expectedHeight, $map->dimensions->height);
    }
}
