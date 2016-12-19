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

namespace PhpMud\Skill;

use PhpMud\Ability;
use PhpMud\CreationGroup;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\TargetType;
use PhpMud\Job\Job;

class FastHealing implements Ability, CreationGroup
{
    public function getCreationPoints(Job $job): int
    {
        switch ((string)$job) {
            case Job::WARRIOR:
                return 4;
            case Job::THIEF:
                return 6;
            case Job::CLERIC:
                return 5;
            case JOB::MAGE:
            default:
                return 8;
        }
    }

    public function getLevel(Job $job): int
    {
        switch ((string)$job) {
            case Job::WARRIOR:
                return 6;
            case Job::THIEF:
                return 16;
            case Job::CLERIC:
                return 15;
            case JOB::MAGE:
            default:
                return 9;
        }
    }

    public function getMinimumDisposition(): Disposition
    {
        return Disposition::SLEEPING();
    }

    public function getTargetType(): TargetType
    {
        return TargetType::NONE();
    }

    public function __toString(): string
    {
        return 'fast healing';
    }
}
