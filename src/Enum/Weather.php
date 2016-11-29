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
 * @method static CLEAR()
 * @method static CLOUDY()
 * @method static RAINY()
 * @method static STORMING()
 */
class Weather extends Enum
{
    const CLEAR = 'clear';

    const CLOUDY = 'cloudy';

    const RAINY = 'rainy';

    const STORMING = 'storming';

    public function getVisibility(): int
    {
        switch ($this->value) {
            case self::CLEAR:
                return 10;
            case self::CLOUDY:
                return 5;
            case self::RAINY:
                return 0;
            case self::STORMING:
                return -5;
        }
    }
}
