<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use Intervention\Image\ImageManager;

use function method_exists;

final readonly class Stitcher
{
    private bool $imageV4;

    /** @api */
    public function __construct(private ImageManager $imageManager)
    {
        /** @phpstan-ignore function.alreadyNarrowedType */
        $this->imageV4 = method_exists($this->imageManager, 'decodeBinary');
    }

    private const string PLACEMENT_POSITION = 'top-left';
    private const int IMAGE_OUTPUT_QUALITY  = 0;
    private const int OFFSET                = 1;

    /** @api */
    public function stitch(string $mimeType, Map $map): string
    {
        /** @phpstan-ignore method.notFound,method.dynamicName */
        $image = $this->imageManager->{($this->imageV4 ? 'createImage' : 'create')}($map->dimensions->width, $map->dimensions->height);

        foreach ($map->tiles as $tile) {
            /** @phpstan-ignore method.notFound,method.dynamicName */
            $tileImage = $this->imageManager->{($this->imageV4 ? 'decodeBinary' : 'read')}($tile->loader->load());
            /**
             * @infection-ignore-all
             *@phpstan-ignore method.nonObject,method.nonObject,method.nonObject,method.nonObject
             */
            if ($tileImage->size()->width() !== $map->tileSize->width || $tileImage->size()->height() !== $map->tileSize->height) {
                /** @phpstan-ignore method.nonObject */
                $tileImage = $tileImage->resize($map->tileSize->width, $map->tileSize->height);
            }

            if ($this->imageV4) {
                /** @phpstan-ignore method.nonObject */
                $image->insert(
                    $tileImage,
                    (($tile->coordinate->x + self::OFFSET) * $map->tileSize->width) - (($map->lowest->x + self::OFFSET) * $map->tileSize->width),
                    (($tile->coordinate->y + self::OFFSET) * $map->tileSize->height) - (($map->lowest->y + self::OFFSET) * $map->tileSize->height),
                    self::PLACEMENT_POSITION,
                );
            } else {
                /** @phpstan-ignore method.nonObject */
                $image->place(
                    $tileImage,
                    self::PLACEMENT_POSITION,
                    (($tile->coordinate->x + self::OFFSET) * $map->tileSize->width) - (($map->lowest->x + self::OFFSET) * $map->tileSize->width),
                    (($tile->coordinate->y + self::OFFSET) * $map->tileSize->height) - (($map->lowest->y + self::OFFSET) * $map->tileSize->height),
                );
            }

            unset($tileImage);
        }

        /** @phpstan-ignore method.dynamicName,return.type,method.nonObject,method.nonObject,method.nonObject */
        return $image->{($this->imageV4 ? 'encodeUsingMediaType' : 'encodeByMediaType')}($mimeType, quality: self::IMAGE_OUTPUT_QUALITY)->toString();
    }
}
