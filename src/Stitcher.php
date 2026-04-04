<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use Intervention\Image\ImageManager;

final readonly class Stitcher
{
    /** @api */
    public function __construct(private ImageManager $imageManager)
    {
    }

    private const string PLACEMENT_POSITION = 'top-left';
    private const int IMAGE_OUTPUT_QUALITY  = 0;
    private const int OFFSET                = 1;

    /** @api */
    public function stitch(string $mimeType, Map $map): string
    {
        $image = $this->imageManager->createImage($map->dimensions->width, $map->dimensions->height);

        foreach ($map->tiles as $tile) {
            $tileImage = $this->imageManager->decodeBinary($tile->loader->load());
            /** @infection-ignore-all */
            if ($tileImage->size()->width() !== $map->tileSize->width || $tileImage->size()->height() !== $map->tileSize->height) {
                $tileImage = $tileImage->resize($map->tileSize->width, $map->tileSize->height);
            }

            $image->insert(
                $tileImage,
                (($tile->coordinate->x + self::OFFSET) * $map->tileSize->width) - (($map->lowest->x + self::OFFSET) * $map->tileSize->width),
                (($tile->coordinate->y + self::OFFSET) * $map->tileSize->height) - (($map->lowest->y + self::OFFSET) * $map->tileSize->height),
                self::PLACEMENT_POSITION,
            );
            unset($tileImage);
        }

        return $image->encodeUsingMediaType($mimeType, quality: self::IMAGE_OUTPUT_QUALITY)->toString();
    }
}
