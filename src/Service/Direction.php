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

namespace PhpMud\Service;

use UnexpectedValueException;
use MyCLabs\Enum\Enum;
use PhpMud\Enum\Direction as DirectionEnum;
use function Functional\first;

class Direction
{
    /**
     * @param string $input
     *
     * @return DirectionEnum
     *
     * @throws UnexpectedValueException
     */
    public function matchPartialString(string $input): DirectionEnum
    {
        $direction = first(DirectionEnum::values(), function (Enum $v) use ($input) {
            return strpos($v->getValue(), $input) === 0;
        });

        if (!$direction) {
            throw new UnexpectedValueException();
        }

        return $direction;
    }
}
