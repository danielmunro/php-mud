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

namespace PhpMud;

use PhpMud\Entity\Mob;
use PhpMud\IO\Output;
use function PhpMud\Dice\dInt;

/**
 * A fight
 */
class Fight
{
    /** @var Mob $mob */
    protected $attacker;

    /** @var Mob $mob */
    protected $target;

    public function __construct(Mob $attacker, Mob $target)
    {
        $this->attacker = $attacker;
        $this->target = $target;
    }

    public function turn()
    {
        if (!$this->isContinuing()) {
            $this->resolve();

            return;
        }

        if ($this->attacker->attackRoll($this->target)) {
            $this->target->modifyHp(-dInt($this->attacker->getAttribute('dam')));
            $this->target->notify(new Output($this->attacker->getName() . "'s clumsy punch hits you.\n"));
            $this->attacker->notify(new Output('Your clumsy punch hits ' . $this->target->getName() . ".\n"));
        } else {
            $this->target->notify(new Output($this->attacker->getName() . "'s clumsy punch misses you.\n"));
            $this->attacker->notify(new Output('Your clumsy punch misses ' . $this->target->getName() . ".\n"));
        }

        if ($this->isContinuing() && $this->target->getFight() === $this) {
            if ($this->target->attackRoll($this->attacker)) {
                $this->attacker->modifyHp(-dInt($this->attacker->getAttribute('dam')));
                $this->attacker->notify(new Output($this->target->getName() . "'s clumsy punch hits you.\n"));
                $this->target->notify(new Output('Your clumsy punch hits ' . $this->target->getName() . "\n"));
            } else {
                $this->attacker->notify(new Output($this->target->getName() . "'s clumsy punch misses you.\n"));
                $this->target->notify(new Output('Your clumsy punch misses ' . $this->attacker->getName() . ".\n"));
            }
        }

        if (!$this->isContinuing()) {
            $this->resolve();

            return;
        }

        if (!$this->target->getFight()) {
            $this->target->setFight($this);
        }
    }

    public function isContinuing(): bool
    {
        return $this->attacker->getHp() > 0 && $this->target->getHp() > 0;
    }

    public function getTarget(): Mob
    {
        return $this->target;
    }

    protected function resolve()
    {
        if ($this->attacker->getHp() <= 0) {
            static::kill($this->target, $this->attacker);
        } elseif ($this->target->getHp() <= 0) {
            static::kill($this->attacker, $this->target);
        }

        $this->attacker->resolveFight();
        $this->target->resolveFight();
    }

    protected static function kill(Mob $killer, Mob $victim)
    {
        $victim->notify(new Output('You have been KILLED!'));
        $debitLevels = $killer->getDebitLevels();
        $killer->notify(
            new Output(
                sprintf(
                    "You killed %s!\nYou gained %d experience.\n",
                    $victim->getName(),
                    $killer->getXpFromKill($victim)
                )
            )
        );
        if ($killer->getDebitLevels() > $debitLevels) {
            $killer->notify(new Output("You qualify for a level!\n"));
        }
    }
}
