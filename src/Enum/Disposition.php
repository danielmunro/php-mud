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
 * @method static STANDING()
 * @method static FIGHTING()
 * @method static SLEEPING()
 */
class Disposition extends Enum
{
    const STANDING = 'standing';
    const FIGHTING = 'fighting';
    const SLEEPING = 'sleeping';

    public function getRegenRate(): float
    {
        switch ($this->value) {
            case self::STANDING:
                return 0.05;
            case self::FIGHTING:
                return 0.0;
            case self::SLEEPING:
                return 0.25;
        }
    }
}
