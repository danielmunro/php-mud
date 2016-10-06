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

use PhpMud\Entity\Mob;
use PhpMud\IO\Output;
use UnexpectedValueException;
use MyCLabs\Enum\Enum;
use PhpMud\Enum\Direction as DirectionEnum;
use function Functional\first;

class DirectionService
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

    /**
     * @param Mob $mob
     * @param \PhpMud\Enum\Direction $direction
     *
     * @return Output
     */
    public function move(Mob $mob, \PhpMud\Enum\Direction $direction): Output
    {
        $sourceRoom = $mob->getRoom();
        $sourceRoom->getMobs()->removeElement($mob);
        $targetDirection = $sourceRoom->getDirections()->filter(function (\PhpMud\Entity\Direction $d) use ($direction) {
            return strpos($d->getDirection(), $direction->getValue()) === 0;
        })->first();
        if (!$targetDirection) {
            return new Output('Alas, that direction does not exist');
        }
        $mob->setRoom($targetDirection->getTargetRoom());
        $mob->getRoom()->getMobs()->add($mob);

        return new Output((string) $mob->getRoom());
    }
}
