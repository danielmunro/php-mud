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
use PhpMud\Entity\Affect;
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

    public function getDelay(): int
    {
        return 2;
    }

    public function rollDice(): int
    {
        return \PhpMud\Dice\d100();
    }

    public function applyFailCost(Mob $mob)
    {
        $mob->modifyMv(-(int)floor($mob->getMv() / 3));
    }

    public function applySuccessCost(Mob $mob)
    {
        $mob->modifyMv(-(int)floor($mob->getMv() / 2));
    }

    public function canPerform(Mob $mob): bool
    {
        return $mob->getMv() > 40;
    }

    public function perform(Input $input): Output
    {
        if (random_int(1, 4) === 1) {
            $input->getMob()->getFight()->getTarget()->addAffect(
                new Affect(\PhpMud\Enum\Affect::STUN, random_int(1, 3))
            );
        }

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
