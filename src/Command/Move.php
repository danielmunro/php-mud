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

use PhpMud\Command;
use PhpMud\Enum\Direction;
use PhpMud\IO\Input;
use PhpMud\IO\Output;

class Move implements Command
{
    /** @var Direction $direction */
    protected $direction;

    /**
     * @param Direction $direction
     */
    public function __construct(Direction $direction)
    {
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Input $input): Output
    {
        $mob = $input->getMob();
        $sourceRoom = $mob->getRoom();
        $targetDirection = $sourceRoom->getDirections()->filter(function (\PhpMud\Entity\Direction $d) {
            return strpos($d->getDirection(), $this->direction->getValue()) === 0;
        })->first();
        if (!$targetDirection) {
            return new Output('Alas, that direction does not exist');
        }
        $sourceRoom->getMobs()->removeElement($mob);
        $mob->setRoom($targetDirection->getTargetRoom());
        $mob->getRoom()->getMobs()->add($mob);

        return new Output((string) $mob->getRoom());
    }
}
