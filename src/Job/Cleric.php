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

namespace PhpMud\Job;

use PhpMud\Entity\Attributes;
use PhpMud\Enum\Ability;
use PhpMud\Enum\Job as JobEnum;
use PhpMud\Skill\Mace;
use PhpMud\Skill\Weapon;

class Cleric implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes([
            'wis' => 2,
            'int' => 1,
            'dex' => -2,
            'str' => -1,
            'cha' => 2
        ]);
    }

    public function getRandomHpGain(): int
    {
        return random_int(7, 10);
    }

    public function getRandomManaGain(): int
    {
        return random_int(15, 25);
    }

    public function getRandomMvGain(): int
    {
        return random_int(10, 15);
    }

    public function getDefaultWeapon(): Ability
    {
        return Ability::MACE();
    }

    public function __toString(): string
    {
        return JobEnum::CLERIC;
    }
}
