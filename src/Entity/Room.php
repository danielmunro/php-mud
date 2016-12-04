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
use PhpMud\Direction\Direction as AbstractDirection;
use function Functional\reduce_left;

/**
 * Class Room
 * @Entity
 * @HasLifecycleCallbacks
 */
class Room
{
    const REGEN_DEFAULT = 0.1;
    const VISIBILITY_DEFAULT = 0;

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

    /** @Column(type="float") */
    protected $regenRate;

    /** @Column(type="boolean") */
    protected $isOutside;

    /** @Column(type="integer") */
    protected $visibility;

    /** @ManyToOne(targetEntity="Area", inversedBy="rooms", cascade={"persist"}) */
    protected $area;

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
        $this->regenRate = self::REGEN_DEFAULT;
        $this->isOutside = true;
        $this->visibility = self::VISIBILITY_DEFAULT;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    public function getDescription(): string
    {
        return $this->description;
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

    public function getRegenRate(): float
    {
        return $this->regenRate;
    }

    public function addRoomInDirection(AbstractDirection $directionEnum, Room $room): Direction
    {
        $direction = new Direction($this, $directionEnum, $room);
        $this->directions->add($direction);

        $reverse = new Direction($room, $directionEnum->reverse(), $this);
        $room->getDirections()->add($reverse);

        return $direction;
    }

    public function isOutside(): bool
    {
        return $this->isOutside;
    }

    public function getVisibility(): int
    {
        return $this->visibility;
    }

    public function setArea(Area $area)
    {
        $this->area = $area;
    }

    public function getArea(): Area
    {
        return $this->area;
    }

    /**
     * @PostLoad
     */
    public function postLoad()
    {
        $this->mobs = $this->mobs->filter(function (Mob $mob) {
            return !$mob->isPlayer();
        });
    }
}
