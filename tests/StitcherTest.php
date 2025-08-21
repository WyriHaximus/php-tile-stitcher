<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\Test;
use WyriHaximus\TestUtilities\TestCase;
use WyriHaximus\TileStitcher\Dimensions;
use WyriHaximus\TileStitcher\Map;
use WyriHaximus\TileStitcher\Stitcher;
use WyriHaximus\TileStitcher\Tile;
use WyriHaximus\TileStitcher\TileLocatorInterface;

use function imagecolorsforindex;
use function Safe\file_get_contents;
use function Safe\imagecolorat;
use function Safe\imagecreatefromstring;

final class StitcherTest extends TestCase
{
    #[Test]
    #[DataProviderExternal(Provider::class, 'tiles')]
    public function render(int $expectedWidth, int $expectedHeight, string $expectedOutput, Tile|TileLocatorInterface ...$tiles): void
    {
        $image = new Stitcher(new ImageManager(new Driver()))->stitch(
            'image/png',
            Map::calculate(
                new Dimensions(512, 512),
                ...$tiles,
            ),
        );

        $this->compareImages($expectedWidth, $expectedHeight, $expectedOutput, $image);
    }

    private function compareImages(int $expectedWidth, int $expectedHeight, string $expectedResult, string $result): void
    {
        $imExpectedResult = imagecreatefromstring(file_get_contents($expectedResult));
        $imResult         = imagecreatefromstring($result);

        for ($x = 0; $x < $expectedWidth; $x += 32) {
            for ($y = 0; $y < $expectedHeight; $y += 32) {
                $rgbExpectedResult    = imagecolorat($imExpectedResult, $x, $y);
                $colorsExpectedResult = imagecolorsforindex($imExpectedResult, $rgbExpectedResult);
                $rgbResult            = imagecolorat($imResult, $x, $y);
                $colorsResult         = imagecolorsforindex($imResult, $rgbResult);

                self::assertSame($colorsExpectedResult['alpha'], $colorsResult['alpha']);
                self::assertSame($colorsExpectedResult['red'], $colorsResult['red']);
                self::assertSame($colorsExpectedResult['green'], $colorsResult['green']);
                self::assertSame($colorsExpectedResult['blue'], $colorsResult['blue']);
            }
        }
    }
}
