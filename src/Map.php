<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use function max;
use function min;

final readonly class Map
{
    private const OFFSET = 1;

    /** @var array<Tile> */
    public array $tiles;

    private function __construct(
        public Dimensions $dimensions,
        public Coordinate $highest,
        public Coordinate $lowest,
        public Dimensions $tileSize,
        Tile ...$tiles,
    ) {
        $this->tiles = $tiles;
    }

    public static function calculate(
        Dimensions $tileSize,
        Tile|TileLocatorInterface $tileOrLocator,
        Tile ...$tiles,
    ): Map {
        /**
         * @psalm-suppress InvalidOperand
         * @var array<Tile> $tiles
         */
        $tiles = [...self::resolveTiles($tileOrLocator, ...$tiles)];

        /** @var non-empty-array<int> $x */
        $x = [];

        /** @var non-empty-array<int> $y */
        $y = [];

        foreach ($tiles as $tile) {
            $x[] = $tile->coordinate->x;
            $y[] = $tile->coordinate->y;
        }

        $lowestX  = min($x);
        $lowestY  = min($y);
        $highestX = max($x);
        $highestY = max($y);

        return new Map(
            new Dimensions(
                ($highestX - $lowestX + self::OFFSET) * $tileSize->width,
                ($highestY - $lowestY + self::OFFSET) * $tileSize->height,
            ),
            new Coordinate($highestX, $highestY),
            new Coordinate($lowestX, $lowestY),
            $tileSize,
            ...$tiles,
        );
    }

    /** @return iterable<Tile> */
    private static function resolveTiles(
        Tile|TileLocatorInterface $tileOrLocator,
        Tile ...$tiles,
    ): iterable {
        if ($tileOrLocator instanceof Tile) {
            yield $tileOrLocator;
            yield from $tiles;

            return;
        }

        yield from $tiles;
        yield from $tileOrLocator->locate();
    }
}
