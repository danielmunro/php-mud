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
 * @method static PIERCE()
 * @method static BASH()
 * @method static SLASH()
 * @method static MAGIC()
 * @method static WEAPON()
 *
 * @method static SUMMON()
 * @method static CHARM()
 * @method static FIRE()
 * @method static COLD()
 * @method static LIGHTNING()
 * @method static ACID()
 * @method static POISON()
 * @method static NEGATIVE()
 * @method static HOLY()
 * @method static ENERGY()
 * @method static MENTAL()
 * @method static DISEASE()
 * @method static DROWNING()
 * @method static LIGHT()
 * @method static SOUND()
 * @method static WOOD()
 * @method static SILVER()
 * @method static IRON()
 * @method static DISTRACTION()
 */
class Vuln extends Enum
{
    const PIERCE = 'pierce';
    const SLASH = 'slash';
    const BASH = 'bash';
    const MAGIC = 'magic';
    const WEAPON = 'weapon';

    const LIGHT = 'light';
    const SOUND = 'sound';
    const WOOD = 'wood';
    const SILVER = 'silver';
    const IRON = 'iron';

    const SUMMON = 'summon';
    const CHARM = 'charm';
    const FIRE = 'fire';
    const COLD = 'cold';
    const LIGHTNING = 'lightning';
    const ACID = 'acid';
    const POISON = 'poison';
    const NEGATIVE = 'negative';
    const HOLY = 'holy';
    const ENERGY = 'energy';
    const MENTAL = 'mental';
    const DISEASE = 'disease';

    const DROWNING = 'drowning';
    const DISTRACTION = 'distraction';
}
