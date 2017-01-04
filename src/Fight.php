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
use function PhpMud\Dice\d20;

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

        $this->multiAttack($this->attacker, $this->target);

        if (!$this->isContinuing()) {
            $this->resolve();

            return;
        }

        if (!$this->target->getFight()) {
            $this->target->setFight(new Fight($this->target, $this->attacker));
        }
    }

    protected function multiAttack(Mob $attacker, Mob $target)
    {
        $this->attack('Reg', $attacker, $target);
        // 2nd
        // 3rd
        // haste
    }

    protected function attack(string $attackName, Mob $attacker, Mob $target)
    {
        if (static::attackRoll($attacker, $target)) {
            $amount = $attacker->getAttribute('dam');
            $target->modifyHp(-dInt($amount));
        } else {
            $amount = 0;
        }
        self::damageMessage($attacker, $target, $attackName, $amount);
    }

    protected static function attackRoll(Mob $attacker, Mob $target): bool
    {
        $hitRoll = d20();
        if ($hitRoll === 1) {
            return false;
        } elseif ($hitRoll < 20) {
            $hitRoll += $attacker->getAttribute('hit') + $target->getAttribute('str');

            if ($hitRoll <= $target->getAttribute('acBash')) {
                return false;
            }
        }

        return true;
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
        $victim->died();
        $debitLevels = $killer->getDebitLevels();
        $killer->notify(
            new Output(
                sprintf(
                    "You killed %s!\nYou gained %d experience.\n",
                    $victim->getName(),
                    (new Experience($killer))->applyFromKill($victim)
                )
            )
        );
        if ($killer->getDebitLevels() > $debitLevels) {
            $killer->notify(new Output("You qualify for a level!\n"));
        }
    }

    protected static function damageMessage(Mob $attacker, Mob $target, string $attackName, int $amount)
    {
        if ($amount === 0) {
            static::notify($attacker, $target, $attackName, 'clumsy', 'misses', ' harmlessly.');
        } elseif ($amount <= 4) {
            static::notify($attacker, $target, $attackName, 'clumsy', Color::cyan('gives'), ' a bruise.');
        } elseif ($amount <= 8) {
            static::notify($attacker, $target, $attackName, 'wobbly', Color::cyan('hits'), ' causing scrapes.');
        }
    }

    protected static function notify(Mob $attacker, Mob $target, string $attackName, $msg1, $msg2, $msg3)
    {
        $attacker->notify(
            new Output(
                sprintf(
                    "(%s) Your %s strike %s %s%s\n",
                    $attackName,
                    $msg1,
                    $msg2,
                    (string)$target,
                    $msg3
                )
            )
        );

        $target->notify(
            new Output(
                sprintf(
                    "%s's %s strike %s you%s\n",
                    (string)$attacker,
                    $msg1,
                    $msg2,
                    $msg3
                )
            )
        );
    }
}
