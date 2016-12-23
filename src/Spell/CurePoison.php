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

namespace PhpMud\Spell;

use PhpMud\Ability\Ability;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\TargetType;
use PhpMud\Job\Job;

class CurePoison implements Spell, Ability
{
    public function getMinimumDisposition(): Disposition
    {
        return Disposition::STANDING();
    }

    public function getTargetType(): TargetType
    {
        return TargetType::DEFENSIVE();
    }

    public function getLevel(Job $job): int
    {
        return 1;
    }

    public function improveDifficultyMultiplier(): int
    {
        return 1;
    }

    public function __toString(): string
    {
        return \PhpMud\Enum\Ability::CURE_POISON;
    }
}
