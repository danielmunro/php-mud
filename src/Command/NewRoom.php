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
use PhpMud\IO\Input;
use PhpMud\IO\Output;

/**
 * Create a new room
 */
class NewRoom implements Command
{
    /**
     * {@inheritdoc}
     */
    public function execute(Input $input): Output
    {
        $dir = $input->getArgs()->last();
        $mob = $input->getMob();
        $srcRoom = $mob->getRoom();
        $newRoom = new Room();
        $newRoom->setTitle('A swirling mist');
        $newRoom->setDescription('You are engulfed by a mist.');
        try {
            $dirEnum = new \PhpMud\Enum\Direction($dir);
        } catch (\UnexpectedValueException $e) {
            return new Output('That direction does not exist');
        }
        $srcDirection = new Direction($srcRoom, $dirEnum, $newRoom);
        $srcRoom->getDirections()->add($srcDirection);

        $newDirection = new Direction($newRoom, $dirEnum->reverse(), $srcRoom);
        $newRoom->getDirections()->add($newDirection);

        return new Output('You wish a room to the '.$dirEnum->getValue());
    }
}