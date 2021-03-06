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
use function PhpMud\Dice\d8;
use function PhpMud\Dice\d10;

class Uninitiated implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes();
    }

    public function getRandomHpGain(): int
    {
        return 8 + d8();
    }

    public function getRandomManaGain(): int
    {
        return 12 + d10();
    }

    public function getRandomMvGain(): int
    {
        return 15 + d10();
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
