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

namespace PhpMud\Command;

use PhpMud\Entity\Mob;
use PhpMud\Entity\Direction;
use PhpMud\IO\Output;
use PhpMud\Service\Direction as DirectionService;

trait Move
{
    /** @var DirectionService $directionService */
    protected $directionService;

    /**
     * @param DirectionService $directionService
     */
    public function __construct(DirectionService $directionService)
    {
        $this->directionService = $directionService;
    }

    /**
     * @param Mob $mob
     * @param \PhpMud\Enum\Direction $direction
     *
     * @return Output
     */
    protected function move(Mob $mob, \PhpMud\Enum\Direction $direction): Output
    {
        $sourceRoom = $mob->getRoom();
        $sourceRoom->getMobs()->removeElement($mob);
        $targetDirection = $sourceRoom->getDirections()->filter(function (Direction $d) use ($direction) {
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
