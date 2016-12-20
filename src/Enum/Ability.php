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
use PhpMud\Ability as AbilityInterface;
use PhpMud\Skill\Bash;
use PhpMud\Skill\Berserk;
use PhpMud\Skill\Dagger;
use PhpMud\Skill\Dodge;
use PhpMud\Skill\FastHealing;
use PhpMud\Skill\HandToHand;
use PhpMud\Skill\Mace;
use PhpMud\Skill\Meditation;
use PhpMud\Skill\Sneak;
use PhpMud\Skill\Sword;
use PhpMud\Skill\Wand;

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
 */
class Ability extends Enum
{
    const BASH = 'bash';
    const BERSERK = 'berserk';
    const DAGGER = 'dagger';
    const DODGE = 'dodge';
    const FAST_HEALING = 'fast healing';
    const HAND_TO_HAND = 'hand to hand';
    const MACE = 'mace';
    const MEDITATION = 'meditation';
    const SNEAK = 'sneak';
    const SWORD = 'sword';
    const WAND = 'wand';

    public static function fromName(string $name): AbilityInterface
    {
        switch ($name) {
            case self::BASH:
                return new Bash();
            case self::BERSERK:
                return new Berserk();
            case self::DAGGER:
                return new Dagger();
            case self::DODGE:
                return new Dodge();
            case self::FAST_HEALING:
                return new FastHealing();
            case self::HAND_TO_HAND:
                return new HandToHand();
            case self::MACE:
                return new Mace();
            case self::MEDITATION:
                return new Meditation();
            case self::SNEAK:
                return new Sneak();
            case self::SWORD:
                return new Sword();
            case self::WAND:
                return new Wand();
            default:
                throw new \UnexpectedValueException(sprintf('unknown skill %s', $name));
        }
    }
}
