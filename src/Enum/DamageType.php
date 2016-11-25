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
 * @method static SLASH()
 * @method static BASH()
 * @method static PIERCE()
 * @method static MAGIC()
 */
class DamageType extends Enum
{
    const SLASH = 'slash';

    const BASH = 'bash';

    const PIERCE = 'pierce';

    const MAGIC = 'magic';
}