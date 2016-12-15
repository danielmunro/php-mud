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
 * @method static NONE()
 * @method static OFFENSIVE()
 * @method static DEFENSIVE()
 * @method static SELF()
 */
class TargetType extends Enum
{
    const NONE = 'none';

    const OFFENSIVE = 'offensive';

    const DEFENSIVE = 'defensive';

    const SELF = 'self';
}
