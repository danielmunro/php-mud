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
use PhpMud\Entity\Direction;
use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\IO\Input;
use PhpMud\IO\Output;

/**
 * Create a new room
 */
class NewRoom implements Command
{
    /** @var DirectionEnum $direction */
    protected $direction;

    /**
     * @param DirectionEnum $direction
     */
    public function __construct(DirectionEnum $direction)
    {
        $this->direction = $direction;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Input $input): Output
    {
        $mob = $input->getMob();
        $srcRoom = $mob->getRoom();
        $newRoom = new Room();
        $newRoom->setTitle('A swirling mist');
        $newRoom->setDescription('You are engulfed by a mist.');

        $srcDirection = new Direction($srcRoom, $this->direction, $newRoom);
        $srcRoom->getDirections()->add($srcDirection);

        $newDirection = new Direction($newRoom, $this->direction->reverse(), $srcRoom);
        $newRoom->getDirections()->add($newDirection);

        return new Output('A room appears to the '.$this->direction->getValue());
    }
}
