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
 * @method static BASH()
 * @method static BERSERK()
 * @method static DAGGER()
 * @method static DODGE()
 * @method static FAST_HEALING()
 * @method static HAND_TO_HAND()
 * @method static MACE()
 * @method static MEDITATION()
 * @method static SNEAK()
 * @method static SWORD()
 * @method static WAND()
 *
 * @method static CURE_LIGHT()
 *
 * @method static MAGIC_MISSILE()
 */
class Ability extends Enum
{
    /**
     * Skills
     */
    const BASH = 'bash';
    const BERSERK = 'berserk';
    const DAGGER = 'dagger';
    const DODGE = 'dodge';
    const FAST_HEALING = 'fast healing';
    const HAND_TO_HAND = 'hand to hand';
    const MACE = 'mace';
    const MEDITATION = 'meditation';
    const SNEAK = 'sneak';
    const STEAL = 'steal';
    const SWORD = 'sword';
    const WAND = 'wand';

    /**
     * Spells
     */
    const CURE_LIGHT = 'cure light';
    const CURE_POISON = 'cure poison';
    const SANCTUARY = 'sanctuary';
    const POISON = 'poison';
    const SLEEP = 'sleep';
    const MAGIC_MISSILE = 'magic missile';
}
