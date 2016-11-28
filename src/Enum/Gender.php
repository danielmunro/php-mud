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
use function Functional\first;

/**
 * @method static MALE()
 * @method static FEMALE()
 * @method static NEUTRAL()
 */
class Gender extends Enum
{
    const MALE = 'male';

    const FEMALE = 'female';

    const NEUTRAL = 'neutral';

    public static function partialSearch(string $partial)
    {
        return first(
            self::values(),
            function (string $gender) use ($partial) {
                return strpos($gender, $partial) === 0;
            }
        );
    }
}