<?php
declare(strict_types=1);

namespace PhpMud\Tests\Enum;

use PhpMud\Enum\Direction;

class DirectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider goodDirectionMatchPartialValueDataProvider
     *
     * @param string $direction
     */
    public function testDirectionMatchPartialValue(string $direction)
    {
        static::assertInstanceOf(Direction::class, Direction::matchPartialValue($direction));
    }

    public function goodDirectionMatchPartialValueDataProvider(): array
    {
        return [
            [
                'n'
            ],
            [
                's'
            ],
            [
                'e'
            ],
            [
                'w'
            ],
            [
                'u'
            ],
            [
                'd'
            ]
        ];
    }

    /**
     * @dataProvider badDirectionMatchPartialValueDataProvider
     *
     * @param string $direction
     */
    public function testDirectionMatchPartialValueFail(string $direction)
    {
        static::assertNull(Direction::matchPartialValue($direction));
    }

    public function badDirectionMatchPartialValueDataProvider(): array
    {
        return [
            [
                'i'
            ],
            [
                'dw'
            ],
            [
                'r'
            ],
            [
                'c'
            ],
            [
                'x'
            ],
            [
                'norht'
            ]
        ];
    }
}
