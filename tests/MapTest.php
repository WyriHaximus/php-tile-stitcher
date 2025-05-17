<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;
use WyriHaximus\TileStitcher\Dimensions;
use WyriHaximus\TileStitcher\Map;
use WyriHaximus\TileStitcher\Tile;
use WyriHaximus\TileStitcher\TileLocatorInterface;

final class MapTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(Provider::class, 'tiles')]
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
