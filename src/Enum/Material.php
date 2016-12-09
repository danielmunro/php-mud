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
 * @method static COPPER()
 * @method static WOOD()
 * @method static BRASS()
 * @method static FOOD()
 */
class Material extends Enum
{
    const COPPER = 'copper';
    const WOOD = 'wood';
    const BRASS = 'brass';
    const FOOD = 'food';
}
