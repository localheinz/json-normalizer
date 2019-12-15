<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/ergebnis/json-normalizer
 */

namespace Ergebnis\Json\Normalizer\Test\Unit\Exception;

use Ergebnis\Json\Normalizer\Exception\InvalidIndentSizeException;

/**
 * @internal
 *
 * @covers \Ergebnis\Json\Normalizer\Exception\InvalidIndentSizeException
 */
final class InvalidIndentSizeExceptionTest extends AbstractExceptionTestCase
{
    public function testExtendsInvalidArgumentException(): void
    {
        self::assertClassExtends(\InvalidArgumentException::class, InvalidIndentSizeException::class);
    }

    public function testFromSizeAndMinimumSizeReturnsInvalidIndentSizeException(): void
    {
        $faker = self::faker();

        $size = $faker->numberBetween(1);
        $minimumSize = $faker->numberBetween(1);

        $exception = InvalidIndentSizeException::fromSizeAndMinimumSize(
            $size,
            $minimumSize
        );

        self::assertInstanceOf(InvalidIndentSizeException::class, $exception);

        $message = \sprintf(
            'Size needs to be greater than %d, but %d is not.',
            $minimumSize - 1,
            $size
        );

        self::assertSame($message, $exception->getMessage());
        self::assertSame($size, $exception->size());
        self::assertSame($minimumSize, $exception->minimumSize());
    }
}
