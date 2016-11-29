<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Time;

class TimeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $hour
     * @param float $expectedVisibility
     *
     * @dataProvider visibilityDataProvider
     */
    public function testVisibility(int $hour, float $expectedVisibility)
    {
        $time = new Time($hour);

        static::assertEquals($expectedVisibility, $time->getVisibility());
    }

    public function visibilityDataProvider()
    {
        return [
            [
                0,
                Time::VISIBILITY_LOW
            ],
            [
                4,
                Time::VISIBILITY_LOW
            ],
            [
                5,
                Time::VISIBILITY_MEDIUM
            ],
            [
                6,
                Time::VISIBILITY_MEDIUM
            ],
            [
                7,
                Time::VISIBILITY_HIGH
            ],
            [
                12,
                Time::VISIBILITY_HIGH
            ],
            [
                19,
                Time::VISIBILITY_HIGH
            ],
            [
                20,
                Time::VISIBILITY_MEDIUM
            ],
            [
                21,
                Time::VISIBILITY_LOW
            ],
            [
                22,
                Time::VISIBILITY_LOW
            ],
            [
                24,
                Time::VISIBILITY_LOW
            ]
        ];
    }
}
