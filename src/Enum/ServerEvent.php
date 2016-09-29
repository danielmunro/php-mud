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
 * Class Event
 * @package PhpMud
 * @method CONNECTION()
 * @method DATA()
 */
class ServerEvent extends Enum
{
    const CONNECTION = 'connection';

    const DATA = 'data';

    const CLOSE = 'close';
}