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
 * @method static MOB()
 * @method static BUILDER()
 * @method static IMMORTAL()
 */
class AccessLevel extends Enum
{
    const MOB = 'mob';
    const BUILDER = 'builder';
    const IMMORTAL = 'immortal';

    public function satisfies(AccessLevel $accessLevel): bool
    {
        return $accessLevel->getIntegerValue() <= $this->getIntegerValue();
    }

    public function getIntegerValue(): int
    {
        switch ($this->value) {
            case self::BUILDER:
                return 1;
            case self::IMMORTAL:
                return 2;
            case self::MOB:
            default:
                return 0;
        }
    }
}
