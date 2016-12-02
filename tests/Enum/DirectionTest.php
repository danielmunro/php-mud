<?php
declare(strict_types=1);

namespace PhpMud\Tests\Enum;

use PhpMud\Direction\Direction;
use PhpMud\Direction\Down;
use PhpMud\Direction\East;
use PhpMud\Direction\North;
use PhpMud\Direction\South;
use PhpMud\Direction\Up;
use PhpMud\Direction\West;

class DirectionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider goodDirectionMatchPartialValueDataProvider
     *
     * @param string $directionValue
     * @param Direction $direction
     */
    public function testDirectionMatchPartialValue(string $directionValue, Direction $direction)
    {
        static::assertEquals((string)$direction, Direction::matchPartialValue($directionValue));
    }

    public function goodDirectionMatchPartialValueDataProvider(): array
    {
        return [
            [
                'n',
                new North()
            ],
            [
                's',
                new South()
            ],
            [
                'e',
                new East()
            ],
            [
                'w',
                new West(),
            ],
            [
                'u',
                new Up()
            ],
            [
                'd',
                new Down()
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
