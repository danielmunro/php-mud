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
 * @method static STANDING()
 * @method static FIGHTING()
 */
class Disposition extends Enum
{
    const STANDING = 'standing';
    const FIGHTING = 'fighting';

    /**
    public function isStanding()
    {
        return $this->getValue() === self::STANDING;
    }
     */
}
