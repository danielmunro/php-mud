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

use PhpMud\Ability\Ability;
use PhpMud\Ability\CreationGroup;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\Job;
use PhpMud\Enum\TargetType;
use PhpMud\Job\Job as JobInterface;

class Dagger implements Ability, Skill, CreationGroup, Weapon
{
    public function getAvailableJobs(): array
    {
        return [
            Job::MAGE(),
            Job::CLERIC(),
            Job::WARRIOR(),
            Job::THIEF()
        ];
    }

    public function getCreationPoints(JobInterface $job): int
    {
        switch ((string)$job) {
            case Job::WARRIOR:
                return 2;
            case Job::THIEF:
                return 2;
            case JOB::MAGE:
                return 2;
            case Job::CLERIC:
            default:
                return 3;
        }
    }

    public function getLevel(JobInterface $job): int
    {
        return 1;
    }

    public function improveDifficultyMultiplier(): int
    {
        return 10;
    }

    public function getMinimumDisposition(): Disposition
    {
        return Disposition::FIGHTING();
    }

    public function getTargetType(): TargetType
    {
        return TargetType::NONE();
    }

    public function __toString(): string
    {
        return \PhpMud\Enum\Ability::DAGGER;
    }
}
