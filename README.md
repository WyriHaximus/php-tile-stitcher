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

use WyriHaximus\TileStitcher\Coordinate;
use WyriHaximus\TileStitcher\Dimensions;
use WyriHaximus\TileStitcher\Stitcher;
use WyriHaximus\TileStitcher\Tile;

$tiles = [
    new Tile(
        new Coordinate(69, 69),
        'map/69_69.png',
    ),
    new Tile(
        new Coordinate(70, 69),
        'map/70_69.png',
    ),
];

Stitcher::stitch(
    Map::calculate(
        new Dimensions(512, 512),
        ...$tiles,
    ),
    'output/two_tile.png',
);
```

The result:

![Two tile stitched image](tests/maps/1x2.png)

# Todo

- [X] `Map::calculateMap` method to calculate the size of the resulting map image
- [X] `Switcher::stitch` method to take the `Map` and stitch it together into an image
- [ ] Pick up desired image format from render output argument, it's PNG only now
- [ ] Support pointing at directory and pick up all images utilizing a callable to parse coordinates
- [ ] Switch to abstraction layer for image operations
- [ ] Dynamic tile sizes + scaling up any tiles smaller than the largest tile

# License

The MIT License (MIT)

Copyright (c) 2024 Cees-Jan Kiewiet

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
