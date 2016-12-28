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

namespace PhpMud\Role;

use PhpMud\Entity\Direction;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Enum\Role as RoleEnum;
use PhpMud\IO\Command\MoveCommand;
use PhpMud\IO\Input;
use function PhpMud\Dice\d20;

class Mobile implements Role
{
    /** @var Room $lastRoom */
    protected $lastRoom;

    public function perform(Mob $mob)
    {
        if (d20() !== 1) {
            return;
        }

        $directions = $mob->getRoom()->getDirections();

        if ($directions->count() === 0) {
            return;
        } elseif ($directions->count() === 1) {
            $direction = $directions->first();
        } else {
            $direction = $this->getRandomDirection($directions->toArray());
            if ($this->lastRoom) {
                while ($direction->getTargetRoom()->getId() === $this->lastRoom->getId()) {
                    $direction = $this->getRandomDirection($directions->toArray());
                }
            }
        }

        $input = new Input((string)$direction);
        $input->setMob($mob);
        $this->lastRoom = $mob->getRoom();
        MoveCommand::move($input, $direction->getDirection());
    }

    public function __toString(): string
    {
        return RoleEnum::MOBILE;
    }

    private function getRandomDirection(array $directions): Direction
    {
        $i = array_rand($directions);

        return $directions[$i];
    }
}
