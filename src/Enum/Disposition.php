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
use PhpMud\IO\Output;

/**
 * @method static SITTING()
 * @method static STANDING()
 * @method static FIGHTING()
 * @method static SLEEPING()
 * @method static FLAT_FOOTED()
 * @method static INCAPACITATED()
 */
class Disposition extends Enum
{
    const SITTING = 'sitting';
    const STANDING = 'standing';
    const FLAT_FOOTED = 'flat-footed';
    const FIGHTING = 'fighting';

    const SLEEPING = 'sleeping';
    const INCAPACITATED = 'incapacitated';

    public function getRegenRate(): float
    {
        switch ($this->value) {
            case self::FIGHTING:
            case self::FLAT_FOOTED:
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
            || $this->value === self::FLAT_FOOTED
            || $this->value === self::FIGHTING;
    }
}
