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
 * @method static CLERIC()
 * @method static MAGE()
 * @method static THIEF()
 * @method static UNINITIATED()
 * @method static WARRIOR()
 */
class Job extends Enum
{
    const CLERIC = 'cleric';
    const MAGE = 'mage';
    const THIEF = 'thief';
    const UNINITIATED = 'uninitiated';
    const WARRIOR = 'warrior';

    public static function matchPartialValue(string $value): Job
    {
        if (strpos(self::CLERIC, $value) === 0) {
            return new Cleric();
        } elseif (strpos(self::MAGE, $value) === 0) {
            return new Mage();
        } elseif (strpos(self::THIEF, $value) === 0) {
            return new Thief();
        } elseif (strpos(self::WARRIOR, $value) === 0) {
            return new Warrior();
        } elseif (strpos(self::UNINITIATED, $value) === 0) {
            return new Uninitiated();
        }

        throw new \UnexpectedValueException(sprintf('unknown value: %s', $value));
    }
}
