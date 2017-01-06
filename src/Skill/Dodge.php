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
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Job\Job as JobInterface;

class Dodge implements Ability, Skill, CreationGroup
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
            case Job::CLERIC:
            case Job::MAGE:
                return 6;
            case Job::WARRIOR:
            case Job::THIEF:
            default:
                return 15;
        }
    }

    public function getLevel(JobInterface $job): int
    {
        switch ((string)$job) {
            case Job::CLERIC:
                return 20;
            case Job::THIEF:
                return 13;
            case Job::WARRIOR:
                return 1;
            case Job::MAGE:
            default:
                return 22;
        }
    }

    public function improveDifficultyMultiplier(): int
    {
        return 6;
    }

    public function perform(Input $input): Output
    {
        return new Output('');
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
        return \PhpMud\Enum\Ability::DODGE;
    }
}
