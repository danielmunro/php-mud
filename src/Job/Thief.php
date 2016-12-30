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

class Thief implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes([
            'wis' => -2,
            'int' => -1,
            'dex' => 2,
            'str' => 1,
            'cha' => 1
        ]);
    }

    public function getRandomHpGain(): int
    {
        return 7 + d6();
    }

    public function getRandomManaGain(): int
    {
        return 8 + d4();
    }

    public function getRandomMvGain(): int
    {
        return 20 + d10();
    }

    public function getDefaultWeapon(): Ability
    {
        return Ability::DAGGER();
    }

    public function __toString(): string
    {
        return JobEnum::THIEF;
    }
}
