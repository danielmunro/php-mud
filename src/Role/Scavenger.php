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
use function Functional\first;
use PhpMud\IO\Output;

class Scavenger implements Role
{
    public function perform(Mob $mob)
    {
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
}
