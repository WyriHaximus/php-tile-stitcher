<?php

declare(strict_types=1);

namespace WyriHaximus\Tests\TileStitcher;

use PHPUnit\Framework\Attributes\Test;
use RuntimeException;
use WyriHaximus\TestUtilities\TestCase;
use WyriHaximus\TileStitcher\FileLoader;

use function bin2hex;
use function random_bytes;

final class FileLoaderTest extends TestCase
{
    #[Test]
    public function nonExistentFileName(): void
    {
        $nonExistantFileName = bin2hex(random_bytes(13));

        self::expectException(RuntimeException::class);
        self::expectExceptionMessage('Unable to load file (' . $nonExistantFileName . '): ');

        (new FileLoader($nonExistantFileName))->load();
    }
}
