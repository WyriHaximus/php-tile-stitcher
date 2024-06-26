<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use Intervention\Image\ImageManager;

final readonly class Stitcher
{
    public function __construct(private ImageManager $imageManager)
    {
    }

    private const PLACEMENT_POSITION   = 'top-left';
    private const IMAGE_OUTPUT_QUALITY = 0;
    private const OFFSET               = 1;

    public function stitch(string $mimeType, Map $map): string
    {
        $image = $this->imageManager->create($map->dimensions->width, $map->dimensions->height);

        foreach ($map->tiles as $tile) {
            $image->place(
                $this->imageManager->read($tile->loader->load()),
                self::PLACEMENT_POSITION,
                (($tile->coordinate->x + self::OFFSET) * $map->tileSize->width) - (($map->lowest->x + self::OFFSET) * $map->tileSize->width),
                (($tile->coordinate->y + self::OFFSET) * $map->tileSize->height) - (($map->lowest->y + self::OFFSET) * $map->tileSize->height),
            );
        }

        return $image->encodeByMediaType($mimeType, quality: self::IMAGE_OUTPUT_QUALITY)->toString();
    }
}
