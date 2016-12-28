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
 * @method static STUN()
 */
class Affect extends Enum
{
    const STUN = 'stun';
    const BERSERK = 'berserk';

    public function getWearOffMessage()
    {
        if ($this->value === static::BERSERK) {
            return 'You feel your pulse slow down.';
        }

        return null;
    }
}
