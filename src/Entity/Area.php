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
use PhpMud\Enum\Visibility;
use PhpMud\Enum\Weather;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Area
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @OneToMany(targetEntity="Room", mappedBy="area") */
    protected $rooms;

    /** @var Weather $weather */
    protected $weather;

    /** @var Visibility $visibility */
    protected $visibility;

    public function __construct(string $name, Visibility $visibility = null)
    {
        $this->name = $name;
        $this->weather = Weather::getRandom();
        $this->rooms = new ArrayCollection();
        $this->visibility = $visibility ?? Visibility::AVERAGE();
    }

    public function getCalculatedVisibility(): int
    {
        return $this->visibility->getValue() + $this->weather->getVisibility();
    }

    public function getVisibility(): Visibility
    {
        return $this->visibility;
    }

    public function setVisibility(Visibility $visibility)
    {
        $this->visibility = $visibility;
    }

    public function addRoom(Room $room)
    {
        $this->rooms->add($room);
        $room->setArea($this);
    }

    public function getRooms(): Collection
    {
        return $this->rooms;
    }

    public function getWeather(): Weather
    {
        return $this->weather;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @PostLoad
     */
    public function setRandomWeather()
    {
        $this->weather = Weather::getRandom();
    }

    /**
     * @PostLoad
     */
    public function reloadVisibilityEnum()
    {
        $this->visibility = new Visibility($this->visibility ?? Visibility::AVERAGE);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
