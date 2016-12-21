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

namespace PhpMud\SpellGroup;

use PhpMud\CreationGroup;
use PhpMud\Enum\Job as JobEnum;
use PhpMud\Job\Job;
use PhpMud\Spell\MagicMissile;

class Combat implements SpellGroup, CreationGroup
{
    public function getAvailableJobs(): array
    {
        return [
            JobEnum::MAGE()
        ];
    }

    public function getCreationPoints(Job $job): int
    {
        return 8;
    }

    public function getSpells(): array
    {
        return [
            new MagicMissile()
        ];
    }
}
