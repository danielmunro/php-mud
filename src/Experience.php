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

namespace PhpMud;

use PhpMud\Entity\Mob;
use function Functional\with;

class Experience
{
    protected $mob;

    public function __construct(Mob $mob)
    {
        $this->mob = $mob;
    }

    public function applyFromKill(Mob $victim): int
    {
        if ($this->mob->getDebitLevels()) {
            return 0;
        }

        $diff = $victim->getLevel() - $this->mob->getLevel();

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

        $xpGain += ($this->mob->getAlignment() > $victim->getAlignment() ?
                $this->mob->getAlignment() - $victim->getAlignment() :
                $victim->getAlignment() - $this->mob->getAlignment()) / 20;

        if ($this->mob->getLevel() < 11) {
            $xpGain += 15 * $xpGain / ($this->mob->getLevel() + 4);
        } elseif ($this->mob->getLevel() > 40) {
            $xpGain += 40 * $xpGain / ($this->mob->getLevel() - 1);
        }

        $xpGain = random_int((int)floor($xpGain * 0.8), (int)ceil($xpGain * 1.2));
        $xpGain = (int)floor(100 + $this->mob->getAttribute('wis') * $xpGain / 100);
        $this->mob->addExperience($xpGain);

        return $xpGain;
    }

    public function levelUp(): int
    {
        $this->mob->levelUp();
        $this->mob->getAttributes()->modifyAttribute(
            'hp',
            with(
                $this->mob->getAttribute('con'),
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
            ) + $this->mob->getJob()->getRandomHpGain()
        );

        $this->mob->getAttributes()->modifyAttribute(
            'mana',
            with(
                $this->mob->getAttribute('wis'),
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
            ) + $this->mob->getJob()->getRandomManaGain()
        );

        $this->mob->getAttributes()->modifyAttribute(
            'mv',
            with(
                $this->mob->getAttribute('dex'),
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
            ) + $this->mob->getJob()->getRandomMvGain()
        );

        return $this->mob->getLevel();
    }

    public function getExperiencePerLevel(): int
    {
        $exp = 1000;
        $cp = $this->mob->getCreationPoints();

        if ($cp < 40) {
            return (int)floor($exp * $this->mob->getRace()->getJobExpMultiplier($this->mob->getJob()) / 100);
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
        $exp *= $this->mob->getRace()->getJobExpMultiplier($this->mob->getJob()) / 100;

        return $exp > 411000 ? 411000 : $exp;
    }
}
