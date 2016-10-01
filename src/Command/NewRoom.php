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
use PhpMud\Input;
use PhpMud\Output;

class NewRoom implements Command
{
    public function execute(Input $input): Output
    {
        $dir = $input->getArgs()->last();
        $mob = $input->getMob();
        $srcRoom = $mob->getRoom();
        $newRoom = new Room();
        $newRoom->setTitle('foo');
        $newRoom->setDescription('foo 2');
        $dirEnum = new \PhpMud\Enum\Direction($dir);
        $srcDirection = new Direction($srcRoom, $dirEnum, $newRoom);
        $srcRoom->getDirections()->add($srcDirection);

        $newDirection = new Direction($newRoom, $dirEnum->reverse(), $srcRoom);
        $newRoom->getDirections()->add($newDirection);

        return new Output('You wish a room to the '.$dirEnum->getValue());
    }
}