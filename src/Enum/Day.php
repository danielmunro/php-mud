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
 * @method static THE_MOON()
 * @method static THE_BULL()
 * @method static DECEPTION()
 * @method static THUNDER()
 * @method static FREEDOM()
 * @method static THE_GREAT_GODS()
 * @method static THE_SUN()
 */
class Day extends Enum
{
    const THE_MOON = 'the Moon';
    const THE_BULL = 'the Bull';
    const DECEPTION = 'Deception';
    const THUNDER = 'Thunder';
    const FREEDOM = 'Freedom';
    const THE_GREAT_GODS = 'the Great Gods';
    const THE_SUN = 'the Sun';

    public static function fromIndex(int $index): self
    {
        return self::values()[self::keys()[$index]];
    }
}
