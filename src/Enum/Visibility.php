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
 * @method static AVERAGE()
 * @method static POOR()
 * @method static GOOD()
 * @method static VERY_GOOD()
 * @method static VERY_POOR()
 */
class Visibility extends Enum
{
    const VERY_POOR = 0;
    const POOR = 10;
    const AVERAGE = 25;
    const GOOD = 40;
    const VERY_GOOD = 60;

    public function __toString(): string
    {
        switch ($this->value) {
            case self::VERY_POOR:
                return 'very poor';
            case self::POOR:
                return 'poor';
            case self::AVERAGE:
                return 'average';
            case self::GOOD:
                return 'good';
            case self::VERY_GOOD:
                return 'very good';
        }
    }
}
