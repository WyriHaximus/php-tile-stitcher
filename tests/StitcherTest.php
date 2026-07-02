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

use function file_get_contents;
use function imagecolorat;
use function imagecolorsforindex;
use function imagecreatefromstring;

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
        $expectedResultFileContents = file_get_contents($expectedResult);
        self::assertIsString($expectedResultFileContents);
        $imExpectedResult = imagecreatefromstring($expectedResultFileContents);
        self::assertNotFalse($imExpectedResult);
        $imResult = imagecreatefromstring($result);
        self::assertNotFalse($imResult);

        for ($x = 0; $x < $expectedWidth; $x += 32) {
            for ($y = 0; $y < $expectedHeight; $y += 32) {
                $rgbExpectedResult = imagecolorat($imExpectedResult, $x, $y);
                self::assertNotFalse($rgbExpectedResult);
                $colorsExpectedResult = imagecolorsforindex($imExpectedResult, $rgbExpectedResult);
                $rgbResult            = imagecolorat($imResult, $x, $y);
                self::assertNotFalse($rgbResult);
                $colorsResult = imagecolorsforindex($imResult, $rgbResult);

                self::assertSame($colorsExpectedResult['alpha'], $colorsResult['alpha']);
                self::assertSame($colorsExpectedResult['red'], $colorsResult['red']);
                self::assertSame($colorsExpectedResult['green'], $colorsResult['green']);
                self::assertSame($colorsExpectedResult['blue'], $colorsResult['blue']);
            }
        }
    }
}
