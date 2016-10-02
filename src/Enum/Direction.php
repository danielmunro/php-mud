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

namespace PhpMud\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static NORTH()
 * @method static SOUTH()
 * @method static EAST()
 * @method static WEST()
 * @method static UP()
 * @method static DOWN()
 */
class Direction extends Enum
{
    const NORTH = 'north';
    const SOUTH = 'south';
    const EAST = 'east';
    const WEST = 'west';
    const UP = 'up';
    const DOWN = 'down';

    public function reverse(): Direction
    {
        switch ($this->getValue()) {
            case static::NORTH():
                return static::SOUTH();
            case static::SOUTH():
                return static::NORTH();
            case static::EAST():
                return static::WEST();
            case static::WEST():
                return static::EAST();
            case static::UP():
                return static::DOWN();
            case static::DOWN():
                return static::UP();
        }
    }
}
