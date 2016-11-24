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

        $this->target->getAttributes()->modifyAttribute('hp', -$this->attacker->getAttribute('dam'));

        if ($this->isContinuing() && $this->target->getFight() === $this) {
            $this->attacker->getAttributes()->modifyAttribute('hp', -$this->target->getAttribute('dam'));
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
