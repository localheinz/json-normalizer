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

use Localheinz\Json\Normalizer\JsonEncodeNormalizer;

final class JsonEncodeNormalizerTest extends AbstractNormalizerTestCase
{
    public function testConstructorRejectsInvalidJsonEncodeOptions()
    {
        $jsonEncodeOptions = -1;

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(\sprintf(
            '"%s" is not valid options for json_encode().',
            $jsonEncodeOptions
        ));

        new JsonEncodeNormalizer($jsonEncodeOptions);
    }

    /**
     * @dataProvider providerJsonEncodeOptions
     *
     * @param int $jsonEncodeOptions
     */
    public function testNormalizeDecodesAndEncodesJsonWithJsonEncodeOptions(int $jsonEncodeOptions)
    {
        $json = <<<'JSON'
{
  "name": "Andreas M\u00f6ller",
  "url": "https:\/\/github.com\/localheinz\/json-normalizer",
  "string-apostroph": "'",
  "string-numeric": "9000",
  "string-quote": "\"",
  "string-tag": "<p>"
}
JSON;
        $normalizer = new JsonEncodeNormalizer($jsonEncodeOptions);

        $encoded = \json_encode(
            \json_decode($json),
            $jsonEncodeOptions
        );

        $this->assertSame($encoded, $normalizer->normalize($json));
    }

    public function providerJsonEncodeOptions(): \Generator
    {
        /**
         * Could add more, but the idea counts.
         */
        $jsonEncodeFlags = [
            JSON_HEX_APOS,
            JSON_HEX_QUOT,
            JSON_HEX_TAG,
            JSON_NUMERIC_CHECK,
            JSON_UNESCAPED_SLASHES,
            JSON_UNESCAPED_UNICODE,
        ];

        $combinations = $this->combinations($jsonEncodeFlags);

        foreach ($combinations as $combination) {
            $jsonEncodeOptions = \array_reduce(
                $combination,
                function (int $jsonEncodeFlag, int $jsonEncodeOptions) {
                    $jsonEncodeOptions |= $jsonEncodeFlag;

                    return $jsonEncodeOptions;
                },
                0
            );

            yield [
                $jsonEncodeOptions,
            ];
        }
    }

    /**
     * @see https://docstore.mik.ua/orelly/webprog/pcook/ch04_25.htm
     *
     * @param array $elements
     *
     * @return array
     */
    private function combinations(array $elements): array
    {
        $combinations = [[]];

        foreach ($elements as $element) {
            foreach ($combinations as $combination) {
                $combinations[] = \array_merge(
                    [
                        $element,
                    ],
                    $combination
                );
            }
        }

        return $combinations;
    }
}
