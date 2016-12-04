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

namespace PhpMud\Fixture;

use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Entity\Shopkeeper;

class MobFixture extends Fixture
{
    protected $mob;

    public function __construct(Mob $mob)
    {
        $this->mob = $mob;
    }

    public function setLook(string $look): self
    {
        $this->mob->setLook($look);

        return $this;
    }

    public function setRoom(Room $room): self
    {
        $this->mob->setRoom($room);

        return $this;
    }

    public function addItem(Item $item): self
    {
        if ($this->mob instanceof Shopkeeper) {
            $this->mob->getShopInventory()->add($item);
        } else {
            $this->mob->getInventory()->add($item);
        }

        return $this;
    }

    public function getInstance(): Mob
    {
        return $this->mob;
    }
}
