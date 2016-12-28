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

use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Role as RoleEnum;
use PhpMud\IO\Output;
use function Functional\first;
use function PhpMud\Dice\d6;

class Scavenger implements Role
{
    public function perform(Mob $mob)
    {
        if (d6() !== 1) {
            return;
        }

        first(
            $mob->getRoom()->getInventory()->getItems(),
            function (Item $item) use ($mob) {
                if ($mob->getInventory()->hasCapacityToAdd($item)) {
                    $mob->getRoom()->getInventory()->remove($item);
                    $mob->getInventory()->add($item);
                    $mob->getRoom()->notify(
                        $mob,
                        new Output(sprintf("%s picks up %s.\n", (string)$mob, (string)$item))
                    );

                    return $item;
                }

                return null;
            }
        );
    }

    public function __toString(): string
    {
        return RoleEnum::SCAVENGER;
    }
}
