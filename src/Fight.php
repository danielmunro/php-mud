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
        } else if ($hitRoll < 20) {
            $hitRoll += $this->attacker->getAttribute('hit') + $this->attacker->getAttribute('str');

            if ($hitRoll <= $this->target->getAttribute('acBash')) {
                return;
            }
        }

        $dam = Dice::dInt($this->attacker->getAttribute('dam'));
        $this->target->getAttributes()->modifyAttribute('hp', -$dam);

        if ($this->isContinuing() && $this->target->getFight() === $this) {
            $dam = Dice::dInt($this->attacker->getAttribute('dam'));
            $this->attacker->getAttributes()->modifyAttribute('hp', -$dam);
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
        return $this->attacker->getAttribute('hp') > 0 && $this->target->getAttribute('hp') > 0;
    }

    protected function resolve()
    {
        $this->attacker->resolveFight();
        $this->target->resolveFight();
    }
}
