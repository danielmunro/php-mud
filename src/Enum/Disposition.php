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

namespace PhpMud\Enum;

use MyCLabs\Enum\Enum;

/**
 * @method static SITTING()
 * @method static STANDING()
 * @method static FIGHTING()
 * @method static SLEEPING()
 * @method static STUNNED()
 * @method static INCAPACITATED()
 * @method static DEAD()
 */
class Disposition extends Enum
{
    const SITTING = 'sitting';
    const STANDING = 'standing';
    const STUNNED = 'stunned';
    const FIGHTING = 'fighting';

    const SLEEPING = 'sleeping';

    const INCAPACITATED = 'incapacitated';
    const DEAD = 'dead';

    public function getRegenRate(): float
    {
        switch ($this->value) {
            case self::FIGHTING:
            case self::STUNNED:
                return 0.0;
            case self::STANDING:
                return 0.05;
            case self::SITTING:
                return 0.15;
            case self::SLEEPING:
                return 0.25;
        }

        return 0.0;
    }

    public function canInteract(): bool
    {
        return $this->value === self::SITTING
            || $this->value === self::STANDING
            || $this->value === self::STUNNED
            || $this->value === self::FIGHTING;
    }

    public function satisfiesMinimumDisposition(Disposition $minimum)
    {
        return $this->getComparator() >= $minimum->getComparator();
    }

    private function getComparator(): int
    {
        switch ($this->value) {
            case self::DEAD:
                return 0;
            case self::INCAPACITATED:
                return 1;
            case self::STUNNED:
                return 2;
            case self::SLEEPING:
                return 3;
            case self::SITTING:
                return 4;
            case self::FIGHTING:
                return 5;
            case self::STANDING:
                return 6;
            default:
                throw new \UnexpectedValueException($this->value);
        }
    }
}
