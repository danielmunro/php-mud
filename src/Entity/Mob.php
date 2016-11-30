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

use PhpMud\Client;
use PhpMud\Dice;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\Gender;
use PhpMud\Fight;
use PhpMud\IO\Output;
use PhpMud\Noun;
use PhpMud\Race\Race;

/**
 * @Entity
 * @HasLifecycleCallbacks
 */
class Mob implements Noun
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string", nullable=true) */
    protected $look;

    /** @Column(type="string") */
    protected $disposition;

    /** @Column(type="array") */
    protected $identifiers;

    /**
     * @ManyToOne(targetEntity="Room", inversedBy="mobs")
     * @var Room $room
     */
    protected $room;

    /** @OneToOne(targetEntity="Attributes", cascade={"persist"})  */
    protected $attributes;

    /** @OneToOne(targetEntity="Inventory", cascade={"persist"}) */
    protected $inventory;

    /** @Column(type="string") */
    protected $race;

    /** @OneToMany(targetEntity="Affect", mappedBy="mob") */
    protected $affects;

    /** @Column(type="integer") */
    protected $hp;

    /** @Column(type="integer") */
    protected $mana;

    /** @Column(type="integer") */
    protected $mv;

    /** @Column(type="boolean") */
    protected $isPlayer;

    /** @Column(type="string", nullable=true) */
    protected $gender;

    /** @var Fight $fight */
    protected $fight;

    /** @var Client $client */
    protected $client;

    /**
     * @param string $name
     * @param Race $race
     */
    public function __construct(string $name, Race $race)
    {
        $this->name = $name;
        $this->identifiers = explode(' ', $name);
        $this->race = $race;
        $this->attributes = $race->getStartingAttributes();
        $this->hp = $this->attributes->getAttribute('hp');
        $this->mana = $this->attributes->getAttribute('mana');
        $this->mv = $this->attributes->getAttribute('mv');
        $this->inventory = new Inventory();
        $this->disposition = Disposition::STANDING();
        $this->isPlayer = false;
        $this->gender = Gender::NEUTRAL();
    }

    public function attackRoll(Mob $target): bool
    {
        $hitRoll = Dice::d20();
        if ($hitRoll === 1) {
            return false;
        } elseif ($hitRoll < 20) {
            $hitRoll += $this->attributes->getAttribute('hit') + $this->attributes->getAttribute('str');

            if ($hitRoll <= $target->getAttribute('acBash')) {
                return false;
            }
        }

        return true;
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

    public function getDisposition(): Disposition
    {
        return $this->disposition;
    }

    public function setDisposition(Disposition $disposition)
    {
        $this->disposition = $disposition;
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

    public function setClient(Client $client)
    {
        $this->client = $client;
        $this->isPlayer = true;
    }

    public function notify(Output $output)
    {
        if ($this->client) {
            $this->client->write((string) $output);
        }
    }

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getMana(): int
    {
        return $this->mana;
    }

    public function getMv(): int
    {
        return $this->mv;
    }

    public function modifyHp(int $amount)
    {
        $this->hp += $amount;

        if ($this->hp > $this->attributes->getAttribute('hp')) {
            $this->hp = $this->attributes->getAttribute('hp');
        }
    }

    public function modifyMana(int $amount)
    {
        $this->mana += $amount;

        if ($this->mana > $this->attributes->getAttribute('mana')) {
            $this->mana = $this->attributes->getAttribute('mana');
        }
    }

    public function modifyMv(int $amount)
    {
        $this->mv += $amount;

        if ($this->mv > $this->attributes->getAttribute('mv')) {
            $this->mv = $this->attributes->getAttribute('mv');
        }
    }

    public function regen()
    {
        $regenBase = $this->room->getRegenRate();

        $this->modifyHp((int) floor($this->attributes->getAttribute('hp') * $regenBase));
        $this->modifyMana((int) floor($this->attributes->getAttribute('mana') * $regenBase));
        $this->modifyMv((int) floor($this->attributes->getAttribute('mv') * $regenBase));
    }

    public function isPlayer(): bool
    {
        return $this->isPlayer;
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender)
    {
        $this->gender = $gender;
    }

    public function getRace(): Race
    {
        return $this->race;
    }

    /**
     * @PostLoad
     * @PostPersist
     */
    public function postLoad()
    {
        $this->race = Race::fromValue((string)$this->race);
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
