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
 * @method static TICKS_PER_DAY()
 */
class Time extends Enum
{
    const TICKS_PER_DAY = 24;

    const DAYS_PER_WEEK = 7;

    const WEEKS_PER_MONTH = 5;

    const MONTHS_PER_YEAR = 12;
}