<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use WyriHaximus\TestUtilities\TestCase;
use WyriHaximus\TileStitcher\Dimensions;
use WyriHaximus\TileStitcher\Map;
use WyriHaximus\TileStitcher\Stitcher;
use WyriHaximus\TileStitcher\Tile;

use function imagecolorsforindex;
use function md5;
use function Safe\file_get_contents;
use function Safe\imagecolorat;
use function Safe\imagecreatefromstring;
use function Safe\imagedestroy;
use function Safe\unlink;
use function time;

final class StitcherTest extends TestCase
{
    /**
     * @test
     * @dataProvider \WyriHaximus\Tests\TileStitcher\Provider::tiles
     */
    public function render(int $expectedWidth, int $expectedHeight, string $expectedOutput, Tile ...$tiles): void
    {
        $output = $this->getTmpDir() . time() . '-' . md5($expectedOutput) . '.png';
        (new Stitcher(new ImageManager(new Driver())))->stitch(
            Map::calculate(
                new Dimensions(512, 512),
                ...$tiles,
            ),
            $output,
        );

        $this->compareImages($expectedWidth, $expectedHeight, $expectedOutput, $output);

        unlink($output);
    }

    private function compareImages(int $expectedWidth, int $expectedHeight, string $expectedResult, string $fileResult): void
    {
        $imExpectedResult = imagecreatefromstring(file_get_contents($expectedResult));
        $imResult         = imagecreatefromstring(file_get_contents($fileResult));

        for ($x = 0; $x < $expectedWidth; $x += 32) {
            for ($y = 0; $y < $expectedHeight; $y += 32) {
                $rgbExpectedResult    = imagecolorat($imExpectedResult, $x, $y);
                $colorsExpectedResult = imagecolorsforindex($imExpectedResult, $rgbExpectedResult);
                $rgbResult            = imagecolorat($imResult, $x, $y);
                $colorsResult         = imagecolorsforindex($imResult, $rgbResult);

                self::assertEquals($colorsExpectedResult['alpha'], $colorsResult['alpha']);
                self::assertEquals($colorsExpectedResult['red'], $colorsResult['red']);
                self::assertEquals($colorsExpectedResult['green'], $colorsResult['green']);
                self::assertEquals($colorsExpectedResult['blue'], $colorsResult['blue']);
            }
        }

        imagedestroy($imExpectedResult);
        imagedestroy($imResult);
    }
}
