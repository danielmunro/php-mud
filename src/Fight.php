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

        $hitRoll = Dice::d20();
        if ($hitRoll === 1) {
            return;
        } elseif ($hitRoll < 20) {
            $hitRoll += $this->attacker->getAttribute('hit') + $this->attacker->getAttribute('str');

            if ($hitRoll <= $this->target->getAttribute('acBash')) {
                return;
            }
        }

        $dam = Dice::dInt($this->attacker->getAttribute('dam'));
        $this->target->modifyHp(-$dam);
        $this->target->notify(new Output($this->attacker->getName()."'s clumsy punch hits you.\n"));
        $this->attacker->notify(new Output('Your clumsy punch hits '.$this->target->getName().".\n"));

        if ($this->isContinuing() && $this->target->getFight() === $this) {
            $dam = Dice::dInt($this->attacker->getAttribute('dam'));
            $this->attacker->modifyHp(-$dam);
            $this->attacker->notify(new Output($this->attacker->getName()."'s clumsy punch hits you.\n"));
            $this->target->notify(new Output('Your clumsy punch hits '.$this->target->getName()."\n"));
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
            $this->attacker->notify(new Output('You have been KILLED!'));
            $this->target->notify(new Output('You killed '.$this->attacker->getName().'!'));
        } elseif ($this->target->getHp() <= 0) {
            $this->target->notify(new Output('You have been KILLED!'));
            $this->attacker->notify(new Output('You killed '.$this->target->getName().'!'));
        }

        $this->attacker->resolveFight();
        $this->target->resolveFight();
    }
}
