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
use PhpMud\Fight;
use PhpMud\IO\Output;
use PhpMud\Job\Job;
use PhpMud\Job\JobFactory;
use PhpMud\Job\Uninitiated;
use PhpMud\Noun;
use PhpMud\Race\Race;
use PhpMud\Role\Roles;
use PhpMud\Skill\FastHealing;
use function PhpMud\Dice\d20;
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

    /** @Column(type="string") */
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

    /** @var int $ageTimer */
    protected $ageTimer;

    /** @var Fight $fight */
    protected $fight;

    /** @var Client $client */
    protected $client;

    /** @var int $delay */
    protected $delay = 0;

    /**
     * @param string $name
     * @param Race $race
     */
    public function __construct(string $name, Race $race)
    {
        $this->name = $name;
        $this->identifiers = explode(' ', $name);
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

    public function setRace(Race $race)
    {
        $this->race = $race;
        each(
            $this->race->getBonusSkills(),
            function (AbilityEnum $ability) {
                $this->abilities->add(new Ability($this, $ability, 1));
            }
        );
    }

    public function setName(string $name, array $identifiers = [])
    {
        $this->name = $name;

        if (!$identifiers) {
            $identifiers = Dictionary::nonFunctionWords($this->name);
        }

        $this->identifiers = $identifiers;
    }

    public function decrementAffects()
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

    public function pulse()
    {
        if ($this->delay > 0) {
            $this->delay--;
        }

        if ($this->fight) {
            $this->fight->turn();
        }

        each ($this->roles, function (string $roleName) {
            $role = Roles::getRole($roleName);
            if ($role->doesWantToPerformRoll()) {
                $role->perform($this);
            }
        });
    }

    public function attackRoll(Mob $target): bool
    {
        $hitRoll = d20();
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

    public function getLongDescription(): string
    {
        return $this->look ?? '%s is here.';
    }

    public function getLook(): string
    {
        return $this->look ?? '%s is here.';
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
        return $this->attributes->getAttribute($attribute) + reduce_left(
                $this->affects->toArray(),
                function (Affect $affect, int $index, array $collection, int $reduction) use ($attribute) {
                    return $reduction + $affect->getAttribute($attribute);
                },
                0
            );
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
        if (!$this->room) {
            return;
        }

        $regenBase = $this->room->getRegenRate() + $this->disposition->getRegenRate();

        $this->modifyHp($this->hpGain($regenBase));
        $this->modifyMana($this->manaGain($regenBase));
        $this->modifyMv($this->mvGain($regenBase));
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
                return 'is bleeding to death.';
        }
    }

    public function hpGain(float $base): int
    {
        $amount = max(3, $base * $this->attributes->getAttribute('hp'));
        $amount += max(0, 15 - $this->attributes->getAttribute('con'));
        $amount += $this->level / 2;
        $amount += $this->withAbility(FastHealing::class, function () {
            return dInt($this->level);
        });

        return (int)floor($amount);
    }

    public function manaGain(float $base): int
    {
        $amount = max(3, $base * $this->attributes->getAttribute('hp'));
        $amount += random_int(1, $this->attributes->getAttribute('wis'));
        $amount += $this->level / 2;
        $amount += $this->withAbility(Meditation::class, function () {
            return dInt($this->level);
        });

        return (int)floor($amount);
    }

    public function mvGain(float $base): int
    {
        $amount = max(3, $base * $this->attributes->getAttribute('hp'));
        $amount += random_int(1, $this->attributes->getAttribute('wis'));
        $amount += $this->level / 2;

        return (int)floor($amount);
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
        $exp = 1000;
        $cp = $this->creationPoints;

        if ($cp < 40) {
            return (int)floor($exp * $this->race->getJobExpMultiplier($this->job) / 100);
        }

        $increment = 500;
        $cp -= 40;

        while ($cp > 9) {
            $exp += $increment;
            $cp -= 10;
            if ($cp > 9) {
                $exp += $increment;
                $increment *= 2;
                $cp -= 10;
            }
        }

        $exp += $cp * $increment / 10;
        $exp *= $this->race->getJobExpMultiplier($this->job) / 100;

        return $exp > 411000 ? 411000 : $exp;
    }

    public function getExperienceToLevel(): int
    {
        return $this->getExperiencePerLevel() - (int)floor($this->experience / $this->level);
    }

    public function getCreationPoints(): int
    {
        return $this->creationPoints;
    }

    public function getXpFromKill(Mob $victim): int
    {
        if ($this->debitLevels) {
            return 0;
        }

        $diff = $victim->getLevel() - $this->level;

        if ($diff < -8) {
            $xpGain = 0;
        } elseif ($diff > 5) {
            $xpGain = 320 + 30 * ($diff - 5);
        } else {
            $xpGain = [
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

        $xpGain += ($this->alignment > $victim->getAlignment() ?
            $this->alignment - $victim->getAlignment() : $victim->getAlignment() - $this->alignment) / 20;

        if ($this->level < 11) {
            $xpGain += 15 * $xpGain / ($this->level + 4);
        } elseif ($this->level > 40) {
            $xpGain += 40 * $xpGain / ($this->level - 1);
        }

        $xpGain = random_int((int)floor($xpGain * 0.8), (int)ceil($xpGain * 1.2));

        $xpGain = (int)floor(100 + $this->getAttribute('wis') * $xpGain / 100);

        $this->experience += $xpGain;

        if ($this->getExperienceToLevel() < 0) {
            $this->debitLevels++;
        }

        return $xpGain;
    }

    public function getLevel(): int
    {
        return $this->level;
    }

    public function getDebitLevels(): int
    {
        return $this->debitLevels;
    }

    public function levelUp(): int
    {
        if (!$this->debitLevels || $this->level >= self::MAX_LEVEL || !$this->isPlayer()) {
            return $this->level;
        }

        $this->debitLevels--;
        $this->level++;

        $this->attributes->modifyAttribute(
            'hp',
            with(
                $this->getAttribute('con'),
                function (int $stat) {
                    if ($stat < 15) {
                        return 0;
                    } elseif ($stat === 15) {
                        return 1;
                    } elseif ($stat <= 17) {
                        return 2;
                    } elseif ($stat <= 19) {
                        return 3;
                    } elseif ($stat <= 21) {
                        return 4;
                    } elseif ($stat === 22) {
                        return 5;
                    } elseif ($stat === 23) {
                        return 6;
                    } elseif ($stat === 24) {
                        return 7;
                    }

                    return 8;
                }
            ) + $this->job->getRandomHpGain()
        );

        $this->attributes->modifyAttribute(
            'mana',
            with(
                $this->getAttribute('wis'),
                function (int $stat) {
                    if ($stat < 15) {
                        return 0;
                    } elseif ($stat === 15) {
                        return 1;
                    } elseif ($stat <= 17) {
                        return 2;
                    } elseif ($stat <= 19) {
                        return 3;
                    } elseif ($stat <= 21) {
                        return 4;
                    } elseif ($stat === 22) {
                        return 5;
                    } elseif ($stat === 23) {
                        return 6;
                    } elseif ($stat === 24) {
                        return 7;
                    }

                    return 8;
                }
            ) + $this->job->getRandomManaGain()
        );

        $this->attributes->modifyAttribute(
            'mv',
            with(
                $this->getAttribute('dex'),
                function (int $stat) {
                    if ($stat < 15) {
                        return 0;
                    } elseif ($stat === 15) {
                        return 1;
                    } elseif ($stat <= 17) {
                        return 2;
                    } elseif ($stat <= 19) {
                        return 3;
                    } elseif ($stat <= 21) {
                        return 4;
                    } elseif ($stat === 22) {
                        return 5;
                    } elseif ($stat === 23) {
                        return 6;
                    } elseif ($stat === 24) {
                        return 7;
                    }

                    return 8;
                }
            ) + $this->job->getRandomMvGain()
        );

        return $this->level;
    }

    public function addAffect(Affect $affect)
    {
        if (none($this->affects->toArray(), function (Affect $a) use ($affect) {
            return $a->getName() === $affect->getName();
        })) {
            $this->affects->add($affect);
        }
    }

    public function getAffects(): Collection
    {
        return $this->affects;
    }

    public function getRoles(): array
    {
        return $this->roles;
    }

    public function addRole(Role $role)
    {
        $this->roles[] = $role->getValue();
    }

    public function removeRole(Role $role)
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

    public function setJob(Job $job)
    {
        if (!$this->job instanceof Uninitiated) {
            throw new \RuntimeException('Cannot change jobs');
        }

        $this->job = $job;
        $this->abilities->add(new Ability($this, $job->getDefaultWeapon(), 1));
    }

    public function incrementDelay(int $delay)
    {
        $this->delay  += $delay;
    }

    public function getDelay(): int
    {
        return $this->delay;
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
        $this->accessLevel = new AccessLevel((string)$this->accessLevel);
        $this->job = JobFactory::matchPartialValue((string)$this->job);
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
        $this->accessLevel = (string) $this->accessLevel;
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
