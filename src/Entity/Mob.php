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

use PhpMud\Enum\Disposition;
use PhpMud\Enum\Race;
use PhpMud\Fight;
use PhpMud\Noun;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Mob implements Noun
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string") */
    protected $look;

    /** @Column(type="string") */
    protected $disposition;

    /** @Column(type="array") */
    protected $identifiers;

    /** @ManyToOne(targetEntity="Room", inversedBy="mobs") */
    protected $room;

    /** @OneToOne(targetEntity="Attributes", cascade={"persist"})  */
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
        $this->race = $race;
        $this->attributes = $race->getStartingAttributes();
        $this->inventory = new Inventory();
        $this->disposition = Disposition::STANDING();
    }

    public function getLook(): string
    {
        return $this->look ?? 'is here.';
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

    /**
     * @PostLoad
     */
    public function postLoad()
    {
        $this->race = new Race($this->race);
        $this->disposition = new Disposition($this->disposition);
    }

    /**
     * @PrePersist
     */
    public function prePersist()
    {
        $this->race = (string) $this->race;
        $this->disposition = (string) $this->disposition;
    }
}
