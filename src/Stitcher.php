<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use function imagecolorallocatealpha;
use function Safe\file_get_contents;
use function Safe\imagecopy;
use function Safe\imagecreate;
use function Safe\imagecreatefromstring;
use function Safe\imagefill;
use function Safe\imagepng;

use const PNG_NO_FILTER;

final class Stitcher
{
    private const SRC_XY          = 0;
    private const RGB_TRANSPARENT = [0, 0, 0, 127];
    private const OFFSET          = 1;

    public static function stitch(Map $map, string $output): void
    {
        $image = imagecreate($map->dimensions->width, $map->dimensions->height);
        /** @psalm-suppress InvalidArgument */
        imagefill($image, self::SRC_XY, self::SRC_XY, imagecolorallocatealpha($image, ...self::RGB_TRANSPARENT));

        foreach ($map->tiles as $tile) {
            $tileImage = imagecreatefromstring(file_get_contents($tile->fileName));
            imagecopy(
                $image,
                $tileImage,
                (($tile->coordinate->x + self::OFFSET) * $map->tileSize->width) - (($map->lowest->x + self::OFFSET) * $map->tileSize->width),
                (($tile->coordinate->y + self::OFFSET) * $map->tileSize->height) - (($map->lowest->y + self::OFFSET) * $map->tileSize->height),
                self::SRC_XY,
                self::SRC_XY,
                $map->tileSize->width,
                $map->tileSize->height,
            );
        }

        imagepng($image, $output, 0, PNG_NO_FILTER);
    }
}
