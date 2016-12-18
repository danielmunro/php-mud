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
class EyeSight extends Enum
{
    const VERY_GOOD = 35;
    const GOOD = 50;
    const AVERAGE = 60;
    const POOR = 70;
    const VERY_POOR = 80;
}
