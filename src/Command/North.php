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
use PhpMud\Input;
use PhpMud\Output;

class North implements Command
{
    public function execute(Input $input): Output
    {
        $mob = $input->getMob();
        $room = $mob->getRoom();
        $room->getMobs()->removeElement($mob);
        $mob->setRoom($room->getDirections()->filter(function(Direction $d) {
            return strpos($d->getDirection(), 'n') === 0;
        })->first()->getTargetRoom());
        $mob->getRoom()->getMobs()->add($mob);

        return new Output((string) $mob->getRoom());
    }
}