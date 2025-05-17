<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use WyriHaximus\TestUtilities\TestCase;
use WyriHaximus\TileStitcher\CallalbleTileLocator;
use WyriHaximus\TileStitcher\Coordinate;
use WyriHaximus\TileStitcher\FileLoader;
use WyriHaximus\TileStitcher\Tile;

use function bin2hex;
use function random_bytes;

final class CallableTileLocatorTest extends TestCase
{
    #[Test]
    public function invalidDirectory(): void
    {
        $nonExistantDirName = bin2hex(random_bytes(13));

        self::expectException(RuntimeException::class);
        /**
         * On Linux the full error includes ": No such file or directory" at the end.
         * On Windows that is somehow ": No error".
         */
        self::expectExceptionMessage('Unable to list relevant tile sets: dir(' . $nonExistantDirName . '): Failed to open directory');

        $tiles = [
            ...(new CallalbleTileLocator($nonExistantDirName, static fn (): Tile|null => null))->locate(),
        ];

        self::assertCount(0, $tiles);
    }

    #[Test]
    public function yield3NullsAndOneTile(): void
    {
        $count = 0;
        $tiles = [
            /** @phpstan-ignore-next-line */
            ...(new CallalbleTileLocator(__DIR__, static function () use (&$count): Tile|null {
                if ($count++ === 3) {
                    return new Tile(
                        new Coordinate(0, 0),
                        new FileLoader(__FILE__),
                    );
                }

                return null;
            }))->locate(),
        ];

        self::assertCount(1, $tiles);
    }
}
