<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use const PHP_INT_MAX;
use const PHP_INT_MIN;

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
        Tile ...$tiles,
    ): Map {
        $lowestX  = PHP_INT_MAX;
        $lowestY  = PHP_INT_MAX;
        $highestX = PHP_INT_MIN;
        $highestY = PHP_INT_MIN;

        foreach ($tiles as $tile) {
            if ($tile->coordinate->x < $lowestX) {
                $lowestX = $tile->coordinate->x;
            }

            if ($tile->coordinate->y < $lowestY) {
                $lowestY = $tile->coordinate->y;
            }

            if ($tile->coordinate->x > $highestX) {
                $highestX = $tile->coordinate->x;
            }

            if ($tile->coordinate->y <= $highestY) {
                continue;
            }

            $highestY = $tile->coordinate->y;
        }

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
}
