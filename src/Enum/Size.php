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
 * @method static XSMALL()
 * @method static SMALL()
 * @method static MEDIUM()
 * @method static LARGE()
 * @method static XLARGE()
 */
class Size extends Enum
{
    const XSMALL = 0;

    const SMALL = 1;

    const MEDIUM = 2;

    const LARGE = 3;

    const XLARGE = 4;
}
