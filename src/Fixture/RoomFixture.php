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

use PhpMud\Direction\Direction;
use PhpMud\Entity\Area;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;

class RoomFixture extends Fixture
{
    protected $room;

    public function __construct(Room $room)
    {
        $this->room = $room;
    }

    public function addItem(Item $item): self
    {
        $this->room->getInventory()->add($item);

        return $this;
    }

    public function addMob(Mob $mob): self
    {
        $this->room->getMobs()->add($mob);

        return $this;
    }

    public function addRoom(Direction $direction, self $room): self
    {
        $this->room->addRoomInDirection($direction, $room->getInstance());

        return $this;
    }

    public function setArea(Area $area): self
    {
        $area->addRoom($this->room);
        $this->room->setArea($area);

        return $this;
    }

    public function getInstance(): Room
    {
        return $this->room;
    }
}
