<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use RuntimeException;

use function error_get_last;
use function file_get_contents;
use function is_array;
use function is_string;

final readonly class FileLoader implements LoaderInterface
{
    public function __construct(private string $fileName)
    {
    }

    public function load(): string
    {
        /** @phpstan-ignore-next-line Suppressing the error as we already throw on it */
        $contents = @file_get_contents($this->fileName);

        if (! is_string($contents)) {
            $errorSuffix = '';
            $error       = error_get_last();
            if (is_array($error)) {
                $errorSuffix = ': ' . $error['message'];
            }

            throw new RuntimeException('Unable to load file (' . $this->fileName . '): ' . $errorSuffix);
        }

        return $contents;
    }
}
