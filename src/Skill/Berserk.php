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
use PhpMud\Entity\Affect;
use PhpMud\Entity\Attributes;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Disposition;
use PhpMud\Enum\Job as JobEnum;
use PhpMud\Enum\TargetType;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Job\Job;
use PhpMud\Noun;
use PhpMud\Ability\Performable;

class Berserk implements Ability, Skill, CreationGroup, Performable, Noun
{
    public function getAvailableJobs(): array
    {
        return [
            JobEnum::WARRIOR(),
            JobEnum::THIEF()
        ];
    }

    public function getCreationPoints(Job $job): int
    {
        return 5;
    }

    public function getLevel(Job $job): int
    {
        return 18;
    }

    public function improveDifficultyMultiplier(): int
    {
        return 2;
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
        $mob->modifyMv(-20);
        $mob->modifyMana(-40);
    }

    public function applySuccessCost(Mob $mob)
    {
        $mob->modifyMv(-40);
        $mob->modifyMana(-80);
    }

    public function canPerform(Mob $mob): bool
    {
        return $mob->getMv() > 40 && $mob->getMana() > 80;
    }

    public function perform(Input $input): Output
    {
        $modifier = max(1, $input->getMob()->getLevel() / 10);
        $input->getMob()->addAffect(new Affect(
            (string)$this,
            $modifier,
            new Attributes([
                'hit' => max(1, $modifier / 3),
                'dam' => max(1, $modifier / 2),
                'hp' => min(40, $modifier * 10)
            ])
        ));

        $input->getRoom()->notify(
            $input->getMob(),
            new Output(
                sprintf(
                    '%s gets a wild look in %s eyes!',
                    (string)$input->getMob(),
                    $input->getMob()->getGenderPronoun()
                )
            )
        );

        return new Output('You feel your pulse speed up as you are consumed by rage!');
    }

    public function getMinimumDisposition(): Disposition
    {
        return Disposition::FIGHTING();
    }

    public function getTargetType(): TargetType
    {
        return TargetType::NONE();
    }

    public function getIdentifiers(): array
    {
        return [
            $this->__toString()
        ];
    }

    public function getLongDescription(): string
    {
        return 'The ability to enter insane rage in combat.';
    }

    public function __toString(): string
    {
        return \PhpMud\Enum\Ability::BERSERK;
    }
}
