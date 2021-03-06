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

class West extends Direction
{
    public function reverse(): Direction
    {
        return new East();
    }

    public function __toString(): string
    {
        return Direction::WEST;
    }
}
