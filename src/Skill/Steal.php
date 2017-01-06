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

class Steal implements Ability, Skill, CreationGroup
{
    public function getAvailableJobs(): array
    {
        return [
            Job::THIEF()
        ];
    }
    public function getCreationPoints(JobInterface $job): int
    {
        return 4;
    }

    public function getLevel(JobInterface $job): int
    {
        return 5;
    }

    public function improveDifficultyMultiplier(): int
    {
        return 2;
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
        return \PhpMud\Enum\Ability::STEAL;
    }
}
