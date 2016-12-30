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
use function PhpMud\Dice\d4;
use function PhpMud\Dice\d6;
use function PhpMud\Dice\d10;

class Warrior implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes([
            'wis' => -1,
            'int' => -1,
            'dex' => 1,
            'str' => 2
        ]);
    }

    public function getRandomHpGain(): int
    {
        return 10 + d6();
    }

    public function getRandomManaGain(): int
    {
        return 4 + d4();
    }

    public function getRandomMvGain(): int
    {
        return 14 + d10();
    }

    public function getDefaultWeapon(): Ability
    {
        return Ability::SWORD();
    }

    public function __toString(): string
    {
        return JobEnum::WARRIOR;
    }
}
