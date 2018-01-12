<?php

declare(strict_types=1);

/**
 * Copyright (c) 2018 Andreas Möller.
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 *
 * @see https://github.com/localheinz/json-normalizer
 */

namespace Localheinz\Json\Normalizer\Test\Unit;

use Localheinz\Json\Normalizer\IndentNormalizer;
use Localheinz\Json\Normalizer\NormalizerInterface;
use Localheinz\Json\Printer\PrinterInterface;
use Localheinz\Test\Util\Helper;
use PHPUnit\Framework;
use Prophecy\Argument;

final class IndentNormalizerTest extends Framework\TestCase
{
    use Helper;

    public function testImplementsNormalizerInterface()
    {
        $this->assertClassImplementsInterface(NormalizerInterface::class, IndentNormalizer::class);
    }

    /**
     * @dataProvider providerInvalidIndent
     *
     * @param string $indent
     */
    public function testConstructorRejectsInvalidIndent(string $indent)
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not a valid indent.',
            $indent
        ));

        new IndentNormalizer(
            $indent,
            $this->prophesize(PrinterInterface::class)->reveal()
        );
    }

    public function providerInvalidIndent(): \Generator
    {
        $values = [
            'not-whitespace' => $this->faker()->sentence,
            'contains-line-feed' => " \n ",
        ];

        foreach ($values as $key => $value) {
            yield $key => [
                $value,
            ];
        }
    }

    public function testNormalizeRejectsInvalidJson()
    {
        $indent = '  ';

        $json = $this->faker()->realText();

        $normalizer = new IndentNormalizer(
            $indent,
            $this->prophesize(PrinterInterface::class)->reveal()
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid JSON.',
            $json
        ));

        $normalizer->normalize($json);
    }

    public function testNormalizeUsesPrinterToNormalizeJsonWithIndent()
    {
        $indent = '  ';

        $json = <<<'JSON'
{
    "name": "Andreas Möller",
    "url": "https://localheinz.com"
}
JSON;

        $normalized = <<<'JSON'
{
    "name": "Andreas Möller (normalized)",
    "url": "https://localheinz.com"
}
JSON;

        $printer = $this->prophesize(PrinterInterface::class);

        $printer
            ->print(
                Argument::is($json),
                Argument::is($indent)
            )
            ->shouldBeCalled()
            ->willReturn($normalized);

        $normalizer = new IndentNormalizer(
            $indent,
            $printer->reveal()
        );

        $this->assertSame($normalized, $normalizer->normalize($json));
    }
}