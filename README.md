# Test utilities

![Continuous Integration](https://github.com/wyrihaximus/php-tile-stitcher/workflows/Continuous%20Integration/badge.svg)
[![Latest Stable Version](https://poser.pugx.org/wyrihaximus/tile-stitcher/v/stable.png)](https://packagist.org/packages/wyrihaximus/tile-stitcher)
[![Total Downloads](https://poser.pugx.org/wyrihaximus/tile-stitcher/downloads.png)](https://packagist.org/packages/wyrihaximus/tile-stitcher/stats)
[![Type Coverage](https://shepherd.dev/github/WyriHaximus/php-tile-stitcher/coverage.svg)](https://shepherd.dev/github/WyriHaximus/php-tile-stitcher)
[![License](https://poser.pugx.org/wyrihaximus/tile-stitcher/license.png)](https://packagist.org/packages/wyrihaximus/tile-stitcher)

# Installation

To install via [Composer](http://getcomposer.org/), use the command below, it will automatically detect the latest version and bind it with `^`.

```
composer require wyrihaximus/tile-stitcher
```

# Usage

The stitcher needs two things:
* Tile dimensions, it assumes they are all the same size
* A list of tile files to stitch together

```php
<?php

declare(strict_types=1);

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use WyriHaximus\TileStitcher\Coordinate;
use WyriHaximus\TileStitcher\Dimensions;
use WyriHaximus\TileStitcher\FileLoader;
use WyriHaximus\TileStitcher\Stitcher;
use WyriHaximus\TileStitcher\Tile;

$tiles = [
    new Tile(
        new Coordinate(69, 69),
        new FileLoader('map/69_69.png'),
    ),
    new Tile(
        new Coordinate(70, 69),
        new FileLoader('map/70_69.png'),
    ),
];

$stitcher = new Stitcher(
    new ImageManager(
        new Driver(),
    ),
);

$image = $stitcher->stitch(
    'image/png',
    Map::calculate(
        new Dimensions(512, 512),
        ...$tiles,
    ),
);

file_put_contents('output/two_tile.png', $image);
```

The result:

![Two tile stitched image](tests/maps/1x2.png)

# Advanced

## Loaders

The main goal of this package is to take tiles and stitch them together into one, there for all I/O bound operations
have not place in this package. However we can't ignore the fact that we need to load tile images from somewhere. And
since some maps can be massive the `LoaderInterface` is included to do reading I/O. A `FileLoader` is included in this
package to provide the most basic implementation. (And, well, not ship a package without being fully functional 😅.)

For example this is an implementation using [`react/filesystem`](https://github.com/reactphp/filesystem/?tab=readme-ov-file#getcontents):

```php
use React\Filesystem\Node\FileInterface;

use function React\Async\await;

final readonly class ReactFileLoader implements LoaderInterface
{
    public function __construct(private FileInterface $file)
    {
    }

    public function load(): string
    {
        return await($this->file->getContents());
    }
}
```

This is an example using [`Flysystem`](https://flysystem.thephpleague.com/docs/) unlocking S3 and a whole range of
different storage systems:

```php
use League\Flysystem\Filesystem;

final readonly class FLysystemFileLoader implements LoaderInterface
{
    public function __construct(private Filesystem $filesystem, private string $path)
    {
    }

    public function load(): string
    {
        return $this->filesystem->read($this->path);
    }
}
```

## Locators

Locators can be used in stead of passing a static list of tile objects to the `Map::calculate` as a way to handle more
dynamic use cases. This package ships with the `CallalbleTileLocator` to aid with average day usage of this package.
It will pass the path you give it plus the file name into the callable you pass it. The callable can either return a
`Tile` or null on no-op. And it will never go deeper than one level.

```php
$image = $stitcher->stitch(
    'image/png',
    Map::calculate(
        new Dimensions(512, 512),
        new CallalbleTileLocator(
            'map/',
            static function (string $fileName): Tile {
                $fileNameWithoutExtension = str_replace('.png', '', basename($fileName));
                $coords = explode('_', $fileNameWithoutExtension);

                return new Tile(
                    new Coordinate(
                        ...array_map('intval', $coords),
                    ),
                    new FileLoader($fileName),
                );
            }
        ),
    ),
);
```

Any implementation of the `TileLocatorInterface` is only expected to return an iterable of `Tile` instances. The `Map`
will only iterate over it once.

# License

The MIT License (MIT)

Copyright (c) 2025 Cees-Jan Kiewiet

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.
