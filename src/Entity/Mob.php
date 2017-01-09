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
use PhpMud\Client;
use PhpMud\Dictionary;
use PhpMud\Enum\Ability as AbilityEnum;
use PhpMud\Enum\AccessLevel;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\Gender;
use PhpMud\Enum\Role;
use PhpMud\Experience;
use PhpMud\Fight;
use PhpMud\IO\Output;
use PhpMud\Job\Job;
use PhpMud\Job\JobFactory;
use PhpMud\Job\Uninitiated;
use PhpMud\Noun;
use PhpMud\Race\Race;
use PhpMud\Role\Roles;
use PhpMud\Skill\FastHealing;
use function PhpMud\Dice\dInt;
use function Functional\with;
use function Functional\each;
use function Functional\first;
use function Functional\none;
use function Functional\filter;
use function Functional\reduce_left;
use PhpMud\Skill\Meditation;

/**
 * @Entity(repositoryClass="\PhpMud\Repository\MobRepository")
 * @HasLifecycleCallbacks
 */
class Mob implements Noun
{
    use PrimaryKeyTrait;

    const INITIAL_SILVER = 20;

    const MAX_LEVEL = 51;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string", nullable=true) */
    protected $look;

    /**
     * @Column(type="string")
     * @var Disposition $disposition
     */
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

    /** @OneToOne(targetEntity="Inventory", cascade={"persist"}) */
    protected $equipped;

    /**
     * @Column(type="string")
     * @var Race $race
     */
    protected $race;

    /** @OneToMany(targetEntity="Affect", mappedBy="mob", cascade={"persist"}) */
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

    /** @Column(type="integer")  */
    protected $creationPoints;

    /** @Column(type="string")  */
    protected $accessLevel;

    /**
     * @OneToMany(targetEntity="Ability", mappedBy="mob", cascade={"persist"})
     * @var ArrayCollection $abilities
     */
    protected $abilities;

    /**
     * @ManyToOne(targetEntity="Room")
     */
    protected $startRoom;

    /** @var int $ageTimer */
    protected $ageTimer;

    /** @var Fight $fight */
    protected $fight;

    /** @var Client $client */
    protected $client;

    /** @var int $delay */
    protected $delay = 0;

    /** @var int $deathTimer */
    protected $deathTimer;

    /**
     * @param string $name
     * @param Race $race
     */
    public function __construct(string $name, Race $race)
    {
        $this->setName($name);
        $this->creationPoints = $race->getCreationPoints();
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
        $this->experience = 0;
        $this->ageInSeconds = 0;
        $this->trains = 0;
        $this->practices = 0;
        $this->skillPoints = 0;
        $this->debitLevels = 0;
        $this->alignment = 0;
        $this->roles = [];
        $this->disposition = Disposition::STANDING();
        $this->abilities = new ArrayCollection();
        $this->affects = new ArrayCollection();
        $this->accessLevel = AccessLevel::MOB();
        $this->setRace($race);
    }

    public function getAccessLevel(): AccessLevel
    {
        return $this->accessLevel;
    }

    public function setRace(Race $race): void
    {
        $this->race = $race;
        each(
            $this->race->getBonusSkills(),
            function (AbilityEnum $ability) {
                $this->abilities->add(new Ability($this, $ability, 1));
            }
        );
    }

    public function setName(string $name, array $identifiers = []): void
    {
        $this->name = $name;

        if (!$identifiers) {
            $identifiers = Dictionary::nonFunctionWords($this->name);
        }

        $this->identifiers = $identifiers;
    }

    public function decrementAffects(): void
    {
        $this->affects = $this->affects->filter(function (Affect $affect) {
            $affect->decrementTimeout();

            if ($affect->getTimeout() === 0) {
                with($affect->getEnum()->getWearOffMessage(), function (string $message) {
                    $this->notify(new Output($message));
                });
            }

            return $affect->getTimeout() > 0;
        });
    }

    public function pulse(): void
    {
        if (!$this->isAlive()) {
            return;
        }

        if ($this->delay > 0) {
            $this->delay--;
        }

        if ($this->fight) {
            $this->fight->turn();
        }

        each($this->roles, function (string $roleName) {
            $role = Roles::getRole($roleName);
            if ($role->doesWantToPerformRoll()) {
                $role->perform($this);
            }
        });
    }

    public function getLongDescription(): string
    {
        return $this->look ?? sprintf(
            '%s the %s %s.',
            (string)$this,
            (string)$this->race,
            $this->getCondition()
        );
    }

    public function getLook(): string
    {
        return $this->look ?? sprintf('%s is here.', (string)$this);
    }

    public function setLook(string $look): void
    {
        $this->look = $look;
    }

    public function getCondition(): string
    {
        $hpPercent = $this->hp / $this->getAttribute('hp');

        switch ($hpPercent) {
            case $hpPercent >= 1.0:
                return 'is in excellent condition';
            case $hpPercent > 0.9:
                return 'has a few scratches';
            case $hpPercent > 0.75:
                return 'has some small wounds and bruises';
            case $hpPercent > 0.5:
                return 'has quite a few wounds';
            case $hpPercent > 0.3:
                return 'has some big nasty wounds and scratches';
            case $hpPercent > 0.15:
                return 'looks pretty hurt';
            case $hpPercent >= 0.0:
                return 'is in awful condition';
            default:
                return 'is bleeding to death';
        }
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

    public function setDisposition(Disposition $disposition): void
    {
        $this->disposition = $disposition;
    }

    public function isAlive(): bool
    {
        return !$this->disposition->equals(Disposition::DEAD());
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
    public function setRoom(Room $room): void
    {
        if (!$this->startRoom) {
            $this->startRoom = $room;
        }
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
        return $this->attributes->getAttribute($attribute) + reduce_left(
            $this->affects->toArray(),
            function (Affect $affect, int $index, array $collection, int $reduction) use ($attribute) {
                return $reduction + $affect->getAttribute($attribute);
            },
            0
        );
    }

    public function getAttributes(): Attributes
    {
        return $this->attributes;
    }

    public function getInventory(): Inventory
    {
        return $this->inventory;
    }

    public function getItems(): array
    {
        return $this->inventory->getItems();
    }

    public function getEquipped(): Inventory
    {
        return $this->equipped;
    }

    public function getIdentifiers(): array
    {
        return $this->identifiers;
    }

    /**
     * @return Fight
     */
    public function getFight(): ?Fight
    {
        return $this->fight;
    }

    /**
     * @param Fight $fight
     */
    public function setFight(Fight $fight): void
    {
        $this->fight = $fight;
    }

    public function resolveFight(): void
    {
        $this->fight = null;
    }

    public function died()
    {
        $this->room->getMobs()->removeElement($this);
        $this->startRoom->getMobs()->add($this);
        $this->room = $this->startRoom;

        $this->affects = new ArrayCollection();

        if ($this->isPlayer()) {
            $this->hp = 1;
            $this->disposition = Disposition::SITTING();
        } else {
            $this->disposition = Disposition::DEAD();
            $this->deathTimer = 0;
        }
    }

    public function respawn()
    {
        $this->disposition = Disposition::STANDING();
        $this->hp = $this->getAttribute('hp');
        $this->mana = $this->getAttribute('mana');
        $this->mv = $this->getAttribute('mv');
    }

    public function incrementDeathTimer(): int
    {
        return $this->deathTimer++;
    }

    public function setClient(Client $client): void
    {
        $this->client = $client;
        $this->isPlayer = true;
    }

    public function notify(Output $output): void
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

    public function modifyHp(int $amount): void
    {
        $this->hp += $amount;

        if ($this->hp > $this->attributes->getAttribute('hp')) {
            $this->hp = $this->attributes->getAttribute('hp');
        }
    }

    public function modifyMana(int $amount): void
    {
        $this->mana += $amount;

        if ($this->mana > $this->attributes->getAttribute('mana')) {
            $this->mana = $this->attributes->getAttribute('mana');
        }
    }

    public function modifyMv(int $amount): void
    {
        $this->mv += $amount;

        if ($this->mv > $this->attributes->getAttribute('mv')) {
            $this->mv = $this->attributes->getAttribute('mv');
        }
    }

    public function regen(): void
    {
        if (!$this->room) {
            return;
        }

        $regenBase = $this->room->getRegenRate() + $this->disposition->getRegenRate();

        $this->modifyHp($this->hpGain($regenBase));
        $this->modifyMana($this->manaGain($regenBase));
        $this->modifyMv($this->mvGain($regenBase));
    }

    public function getAbilities(): Collection
    {
        return $this->abilities;
    }

    public function withAbility(string $abilityClass, callable $callable)
    {
        return with(
            first(
                $this->abilities->toArray(),
                function (Ability $ability) use ($abilityClass) {
                    return get_class($ability) === $abilityClass;
                }
            ),
            function (Ability $ability) use ($callable) {
                $this->rollAbilityImprovement($ability, $ability->getAbility()->improveDifficultyMultiplier());
                return $callable($ability);
            }
        );
    }

    public function rollAbilityImprovement(Ability $ability, int $multiplier)
    {
        // @todo implement improvement for abilities
    }

    public function isPlayer(): bool
    {
        return $this->isPlayer;
    }

    public function getGenderPronoun(): string
    {
        if ($this->gender === Gender::FEMALE) {
            return 'her';
        } elseif ($this->gender === Gender::MALE) {
            return 'his';
        } else {
            return 'its';
        }
    }

    public function getGender(): Gender
    {
        return $this->gender;
    }

    public function setGender(Gender $gender): void
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

    public function addExperience(int $experience): void
    {
        $this->experience += $experience;

        if ($this->getExperienceToLevel() < 0) {
            $this->debitLevels++;
        }
    }

    public function getExperienceToLevel(): int
    {
        return (new Experience($this))->getExperiencePerLevel() - (int)floor($this->experience / $this->level);
    }

    public function getCreationPoints(): int
    {
        return $this->creationPoints;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getDebitLevels(): int
    {
        return $this->debitLevels;
    }

    public function levelUp(): void
    {
        if (!$this->debitLevels) {
            throw new \RuntimeException('No debit levels available!');
        }

        if (!$this->level >= self::MAX_LEVEL) {
            throw new \RuntimeException('Cannot level beyond the max level!');
        }

        $this->debitLevels--;
        $this->level++;
    }

    public function addAffect(Affect $affect): void
    {
        if (none($this->affects->toArray(), function (Affect $a) use ($affect) {
            return $a->getName() === $affect->getName();
        })) {
            $this->affects->add($affect);
        }
    }

    public function getAffects(): array
    {
        return $this->affects->toArray();
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(Role $role): void
    {
        $this->roles[] = $role->getValue();
    }

    public function removeRole(Role $role): void
    {
        $this->roles = filter(
            $this->roles,
            function (string $roleName) use ($role) {
                return $roleName !== (string)$role;
            }
        );
    }

    public function hasRole(Role $role): bool
    {
        return in_array($role->getValue(), $this->roles, true);
    }

    public function getJob(): Job
    {
        return $this->job;
    }

    public function setJob(Job $job): void
    {
        if (!$this->job instanceof Uninitiated) {
            throw new \RuntimeException('Cannot change jobs');
        }

        $this->job = $job;
        $this->abilities->add(new Ability($this, $job->getDefaultWeapon(), 1));
    }

    public function incrementDelay(int $delay): void
    {
        $this->delay  += $delay;
    }

    public function getDelay(): int
    {
        return $this->delay;
    }

    public function __clone()
    {
        $this->id = null;
        $this->attributes = clone $this->attributes;
        $this->inventory = clone $this->inventory;
        $this->equipped = clone $this->equipped;
    }

    /**
     * @PostLoad
     * @PostPersist
     */
    public function postLoad(): void
    {
        $this->race = Race::fromValue((string)$this->race);
        $this->disposition = new Disposition($this->disposition);
        $this->gender = new Gender((string)$this->gender);
        $this->accessLevel = new AccessLevel((string)$this->accessLevel);
        $this->job = JobFactory::matchPartialValue((string)$this->job);
        $this->ageTimer = time();
    }

    /**
     * @PrePersist
     */
    public function prePersist(): void
    {
        $this->race = (string) $this->race;
        $this->disposition = (string) $this->disposition;
        $this->ageInSeconds += time() - $this->ageTimer;
        $this->accessLevel = (string) $this->accessLevel;
    }

    public function __toString(): string
    {
        return $this->name;
    }

    private function hpGain(float $base): int
    {
        $amount = max(3, $base * $this->attributes->getAttribute('hp'));
        $amount += max(0, 15 - $this->attributes->getAttribute('con'));
        $amount += $this->level / 2;
        $amount += $this->withAbility(FastHealing::class, function () {
            return dInt($this->level);
        });

        return (int)floor($amount);
    }

    private function manaGain(float $base): int
    {
        $amount = max(3, $base * $this->attributes->getAttribute('hp'));
        $amount += random_int(1, $this->attributes->getAttribute('wis')) / 2;
        $amount += random_int(1, $this->attributes->getAttribute('int')) / 3;
        $amount += $this->level / 2;
        $amount += $this->withAbility(Meditation::class, function () {
            return dInt($this->level);
        });

        return (int)floor($amount);
    }

    private function mvGain(float $base): int
    {
        $amount = max(3, $base * $this->attributes->getAttribute('hp'));
        $amount += random_int(1, $this->attributes->getAttribute('wis'));
        $amount += $this->level / 2;

        return (int)floor($amount);
    }
}
