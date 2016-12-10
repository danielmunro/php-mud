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
use PhpMud\Enum\Role;
use PhpMud\Fight;
use PhpMud\IO\Output;
use PhpMud\Job\Job;
use PhpMud\Job\Uninitiated;
use PhpMud\Noun;
use PhpMud\Race\Race;

/**
 * @Entity(repositoryClass="\PhpMud\Repository\MobRepository")
 * @HasLifecycleCallbacks
 */
class Mob implements Noun
{
    use PrimaryKeyTrait;

    const INITIAL_SILVER = 20;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string", nullable=true) */
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

    /** @OneToOne(targetEntity="Inventory", cascade={"persist"}) */
    protected $equipped;

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

    /** @Column(type="integer") */
    protected $experience;

    /** @Column(type="integer") */
    protected $level;

    /** @Column(type="integer") */
    protected $debitLevels;

    /** @Column(type="integer") */
    protected $experiencePerLevel;

    /** @Column(type="integer") */
    protected $ageInSeconds;

    /** @Column(type="array") */
    protected $roles;

    /** @Column(type="integer") */
    protected $trains;

    /** @Column(type="integer") */
    protected $practices;

    /** @Column(type="integer") */
    protected $skillPoints;

    /** @Column(type="string") */
    protected $job;

    /** @Column(type="integer") */
    protected $alignment;

    /** @var int $ageTimer */
    protected $ageTimer;

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
        $this->job = new Uninitiated();
        $this->attributes = $race->getStartingAttributes();
        $this->hp = $this->attributes->getAttribute('hp');
        $this->mana = $this->attributes->getAttribute('mana');
        $this->mv = $this->attributes->getAttribute('mv');
        $this->inventory = new Inventory();
        $this->equipped = new Inventory();
        $this->disposition = Disposition::STANDING();
        $this->isPlayer = false;
        $this->gender = Gender::NEUTRAL();
        $this->level = 1;
        $this->experience = 1;
        $this->experiencePerLevel = 1;
        $this->ageInSeconds = 0;
        $this->trains = 0;
        $this->practices = 0;
        $this->skillPoints = 0;
        $this->debitLevels = 0;
        $this->alignment = 0;
        $this->roles = [];
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

    public function setLook(string $look)
    {
        $this->look = $look;
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

    public function getEquipped(): Inventory
    {
        return $this->equipped;
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

    public function getWeightCapacity(): integer
    {
        return (25 * $this->race->getSize()) + (10 * $this->getAttribute('str'));
    }

    public function getAgeInYears(): int
    {
        return (int)floor(17 + ($this->ageInSeconds / 72000));
    }

    public function getAgeInHours(): int
    {
        return (int)floor($this->ageInSeconds / 3600);
    }

    public function getTrains(): int
    {
        return $this->trains;
    }

    public function getPractices(): int
    {
        return $this->practices;
    }

    public function getSkillPoints(): int
    {
        return $this->skillPoints;
    }

    public function getAlignment(): int
    {
        return $this->alignment;
    }

    public function getExperience(): int
    {
        return $this->experience;
    }

    public function getExperiencePerLevel(): int
    {
        return $this->experiencePerLevel;
    }

    public function getExperienceToLevel(): int
    {
        //return (int)ceil($this->experience / $this->);
        return 1;
    }

    public function gainExperienceFromKill(Mob $victim)
    {
        if ($this->debitLevels) {
            return;
        }

        $diff = $victim->getLevel() - $this->level;

        if ($diff < -8) {
            $base = 0;
        } elseif ($diff > 5) {
            $base = 320 + 30 * ($diff - 5);
        } else {
            $base = [
                -8 => 2,
                -7 => 7,
                -6 => 13,
                -5 => 20,
                -4 => 26,
                -3 => 40,
                -2 => 60,
                -1 => 80,
                0 => 100,
                1 => 140,
                2 => 180,
                3 => 220,
                4 => 280,
                5 => 320
            ][$diff];
        }

        $base += ($this->alignment > $victim->getAlignment() ?
            $this->alignment - $victim->getAlignment() : $victim->getAlignment() - $this->alignment) / 20;

        if ($this->level < 11) {
            $base += 15 * $base / ($this->level + 4);
        } elseif ($this->level > 40) {
            $base += 40 * $base / ($this->level - 1);
        }

        $base = random_int($base * 0.8, $base * 1.2);

        return 100 + $this->getAttribute('wis') * $base / 100;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function addRole(Role $role)
    {
        $this->roles[] = $role->getValue();
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role->getValue(), $this->roles, true);
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job)
    {
        $this->job = $job;
    }

    /**
     * @PostLoad
     * @PostPersist
     */
    public function postLoad()
    {
        $this->race = Race::fromValue((string)$this->race);
        $this->disposition = new Disposition($this->disposition);
        $this->gender = new Gender((string)$this->gender);
        $this->job = Job::matchPartialValue((string)$this->job);
        $this->ageTimer = time();
    }

    /**
     * @PrePersist
     */
    public function prePersist()
    {
        $this->race = (string) $this->race;
        $this->disposition = (string) $this->disposition;
        $this->ageInSeconds += time() - $this->ageTimer;
    }
}
