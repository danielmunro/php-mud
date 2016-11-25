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

use PhpMud\Enum\Race;
use PhpMud\Fight;
use PhpMud\Noun;

/**
 * Class Mob
 * @package PhpMud\Entity
 * @Entity
 */
class Mob implements Noun
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="array") */
    protected $identifiers;

    /** @ManyToOne(targetEntity="Room", inversedBy="mobs") */
    protected $room;

    /** @OneToOne(targetEntity="Attributes")  */
    protected $attributes;

    /** @OneToOne(targetEntity="Inventory", cascade={"persist"}) */
    protected $inventory;

    /** @Column(type="string") */
    protected $race;

    /** @OneToMany(targetEntity="Affect", mappedBy="mob") */
    protected $affects;

    /** @var Fight $fight */
    protected $fight;

    /**
     * @param string $name
     * @param Race $race
     */
    public function __construct(string $name, Race $race)
    {
        $this->name = $name;
        $this->race = $race->getValue();
        $this->attributes = $race->getStartingAttributes();
        $this->inventory = new Inventory();
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return Room
     */
    public function getRoom(): Room
    {
        return $this->room;
    }

    /**
     * @param Room $room
     */
    public function setRoom(Room $room)
    {
        if ($this->room) {
            $this->room->getMobs()->removeElement($this);
        }
        $this->room = $room;
        $room->getMobs()->add($this);
    }

    /**
     * @param string $attribute
     * @return int
     */
    public function getAttribute(string $attribute): int
    {
        return $this->attributes->getAttribute($attribute);
    }

    /**
     * @return Attributes
     */
    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    /**
     * @return Inventory
     */
    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    /**
     * @return array
     */
    public function getIdentifiers(): array
    {
        return $this->identifiers ?? [$this->name];
    }

    /**
     * @return Fight
     */
    public function getFight()
    {
        return $this->fight;
    }

    /**
     * @param Fight $fight
     */
    public function setFight(Fight $fight)
    {
        $this->fight = $fight;
    }

    public function resolveFight()
    {
        $this->fight = null;
    }
}
