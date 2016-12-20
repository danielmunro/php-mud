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
use PhpMud\Enum\Job;
use PhpMud\Enum\TargetType;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Job\Job as JobInterface;

class Sneak implements Ability, Skill, CreationGroup
{
    public function getAvailableJobs(): array
    {
        return [
            Job::THIEF()
        ];
    }

    public function getCreationPoints(JobInterface $job): int
    {
        switch ((string)$job) {
            case Job::THIEF:
                return 4;
            case Job::WARRIOR:
            default:
                return 6;
        }
    }

    public function getLevel(JobInterface $job): int
    {
        switch ((string)$job) {
            case Job::THIEF:
                return 4;
            case Job::WARRIOR:
                return 10;
            default:
                return 45;
        }
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
        return 'sneak';
    }
}
