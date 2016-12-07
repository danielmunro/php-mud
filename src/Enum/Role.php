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
 * @method static SHOPKEEPER()
 * @method static SCAVENGER()
 * @method static GUARD()
 * @method static AGGRESSIVE()
 */
class Role extends Enum
{
    const SHOPKEEPER = 'shopkeeper';

    const SCAVENGER = 'scavenger';

    const GUARD = 'guard';

    const AGGRESSIVE = 'aggressive';
}
