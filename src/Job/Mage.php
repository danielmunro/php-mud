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
use PhpMud\Skill\Wand;
use PhpMud\Skill\Weapon;

class Mage implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes([
            'wis' => 2,
            'int' => 2,
            'str' => -1,
            'dex' => -1,
            'con' => -1
        ]);
    }

    public function getRandomHpGain(): int
    {
        return random_int(6, 8);
    }

    public function getRandomManaGain(): int
    {
        return random_int(20, 30);
    }

    public function getRandomMvGain(): int
    {
        return random_int(8, 12);
    }

    public function getDefaultWeapon(): Ability
    {
        return Ability::WAND();
    }

    public function __toString(): string
    {
        return JobEnum::MAGE;
    }
}
