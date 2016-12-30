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
        return 4 + d4();
    }

    public function getRandomManaGain(): int
    {
        return 20 + d10();
    }

    public function getRandomMvGain(): int
    {
        return 6 + d6();
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
