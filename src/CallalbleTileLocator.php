<?php

declare(strict_types=1);

namespace WyriHaximus\TileStitcher;

use Closure;
use RuntimeException;

use function dir;
use function error_get_last;
use function is_array;

final readonly class CallalbleTileLocator implements TileLocatorInterface
{
    /** @param Closure(string): ?Tile $callable */
    public function __construct(
        private string $directory,
        private Closure $callable,
    ) {
    }

    /** @return iterable<Tile> */
    public function locate(): iterable
    {
        /** @phpstan-ignore-next-line Suppressing the error as we already throw on it */
        $tiles = @dir($this->directory);
        if ($tiles === false) {
            $errorSuffix = '';
            $error       = error_get_last();
            if (is_array($error)) {
                $errorSuffix = ': ' . $error['message'];
            }

            throw new RuntimeException('Unable to list relevant tile sets' . $errorSuffix);
        }

        while (($tile = $tiles->read()) !== false) {
            if ($tile === '.' || $tile === '..') {
                continue;
            }

            $tileOrNot = ($this->callable)($this->directory . $tile);
            if (! ($tileOrNot instanceof Tile)) {
                continue;
            }

            yield $tileOrNot;
        }
    }
}
