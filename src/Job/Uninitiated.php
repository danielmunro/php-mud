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
use PhpMud\Skill\HandToHand;
use PhpMud\Skill\Weapon;
use PhpMud\Enum\Job as JobEnum;

class Uninitiated implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes();
    }

    public function getRandomHpGain(): int
    {
        return random_int(6, 15);
    }

    public function getRandomManaGain(): int
    {
        return random_int(8, 30);
    }

    public function getRandomMvGain(): int
    {
        return random_int(7, 10);
    }

    public function getDefaultWeapon(): Ability
    {
        return Ability::HAND_TO_HAND();
    }

    public function __toString(): string
    {
        return JobEnum::UNINITIATED;
    }
}
