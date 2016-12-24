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
use PhpMud\CreationGroup;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\Job as JobEnum;
use PhpMud\Enum\TargetType;
use PhpMud\Fight;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Job\Job;
use PhpMud\Noun;
use PhpMud\Performable;
use function Functional\with;
use function Functional\first;

class Bash implements Ability, Skill, CreationGroup, Performable, Noun
{
    public function getAvailableJobs(): array
    {
        return [
            JobEnum::WARRIOR()
        ];
    }

    public function getCreationPoints(Job $job): int
    {
        return 4;
    }

    public function getLevel(Job $job): int
    {
        return 1;
    }

    public function improveDifficultyMultiplier(): int
    {
        return 1;
    }

    public function perform(Input $input): Output
    {
        $base = (int)floor(($input->getMob()->getLevel() / 10) + 1);

        $input
            ->getMob()
            ->getFight()
            ->getTarget()
            ->modifyHp(-random_int($base, (int)floor($base * 1.5)));

        return new Output(
            sprintf(
                'You slam into %s and send them flying!',
                $input->getMob()->getFight()->getTarget()->getName()
            )
        );

    }

    public function getMinimumDisposition(): Disposition
    {
        return Disposition::FIGHTING();
    }

    public function getTargetType(): TargetType
    {
        return TargetType::OFFENSIVE();
    }

    public function getIdentifiers(): array
    {
        return [
            $this->__toString()
        ];
    }

    public function __toString(): string
    {
        return \PhpMud\Enum\Ability::BASH;
    }
}
