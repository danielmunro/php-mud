<?php
declare(strict_types=1);

/**
 * This file is part of the PhpMud package.
 *
 * (c) Dan Munro <dan@danmunro.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMud\Direction;

use function Functional\first;

abstract class Direction
{
    const NORTH = 'north';
    const SOUTH = 'south';
    const EAST = 'east';
    const WEST = 'west';
    const UP = 'up';
    const DOWN = 'down';

    public static function matchPartialValue(string $value)
    {
        return first(
            [
                self::NORTH,
                self::SOUTH,
                self::EAST,
                self::WEST,
                self::UP,
                self::DOWN
            ],
            function ($directionValue) use ($value) {
                return strpos($directionValue, $value) === 0;
            }
        );
    }

    abstract public function reverse(): Direction;

    abstract public function __toString(): string;
}
