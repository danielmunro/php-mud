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

namespace PhpMud\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use PhpMud\Enum\Direction as DirectionEnum;
use function Functional\reduce_left;

/**
 * Class Room
 * @package PhpMud\Entity
 * @Entity
 */
class Room
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="text") */
    protected $description;

    /** @OneToMany(targetEntity="Mob", mappedBy="room", cascade={"persist"}) */
    protected $mobs;

    /** @OneToMany(targetEntity="Direction", mappedBy="sourceRoom", cascade={"persist"}) */
    protected $directions;

    /** @OneToOne(targetEntity="Inventory", cascade={"persist"}) */
    protected $inventory;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->mobs = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this->inventory = new Inventory();
        $this->title = '';
        $this->description = '';
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return Collection
     */
    public function getMobs(): Collection
    {
        return $this->mobs;
    }

    /**
     * @return Collection
     */
    public function getDirections(): Collection
    {
        return $this->directions;
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    /**
     * @param DirectionEnum $directionEnum
     * @param Room $room
     */
    public function addRoomInDirection(DirectionEnum $directionEnum, Room $room)
    {
        $direction = new Direction($this, $directionEnum, $room);
        $this->directions->add($direction);

        $reverse = new Direction($room, $directionEnum->reverse(), $this);
        $room->getDirections()->add($reverse);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return
            $this->title."\n".
            $this->description."\n".
            (string) $this->inventory."\n".
            'Exits ['.
            reduce_left(
                $this->directions->toArray(),
                function (Direction $direction, $index, $collection, $reduction) {
                    return $reduction . substr($direction->getDirection(), 0, 1);
                }
            ).
            '] '
            .reduce_left(
                $this->mobs->toArray(),
                function (Mob $mob, $index, $collection, $reduction) {
                    return $reduction."\n".$mob->getName();
                },
                "\n"
            );
    }
}
