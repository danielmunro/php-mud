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
 * @method static LIGHT()
 * @method static FINGER()
 * @method static NECK()
 * @method static TORSO()
 * @method static HEAD()
 * @method static LEGS()
 * @method static FEET()
 * @method static HANDS()
 * @method static ARMS()
 * @method static SHIELD()
 * @method static BODY()
 * @method static WAIST()
 * @method static WRIST()
 * @method static WIELDED()
 * @method static HELD()
 * @method static FLOATING()
 * @method static SECONDARY()
 */
class Position extends Enum
{
    const LIGHT = 'light';
    const FINGER = 'finger';
    const NECK = 'neck';
    const TORSO = 'torso';
    const HEAD = 'head';
    const LEGS = 'legs';
    const FEET = 'feet';
    const HANDS = 'hands';
    const ARMS = 'arms';
    const SHIELD = 'shield';
    const BODY = 'body';
    const WAIST = 'waist';
    const WRIST = 'wrist';
    const WIELDED = 'wielded';
    const HELD = 'held';
    const FLOATING = 'floating';
    const SECONDARY = 'secondary';

    public function getCount(): int
    {
        return
            $this->value === self::FINGER
            || $this->value === self::NECK
            || $this->value === self::WRIST
            ? 2 : 1;
    }
}
