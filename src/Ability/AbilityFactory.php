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

namespace PhpMud\Ability;

use PhpMud\Enum\Ability as AbilityEnum;
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
use PhpMud\Spell\CureLight;
use PhpMud\Spell\CurePoison;
use PhpMud\Spell\MagicMissile;
use PhpMud\Spell\Poison;
use PhpMud\Spell\Sanctuary;

class AbilityFactory
{
    private static $map = [
        AbilityEnum::BASH => Bash::class,
        AbilityEnum::BERSERK => Berserk::class,
        AbilityEnum::DAGGER => Dagger::class,
        AbilityEnum::DODGE => Dodge::class,
        AbilityEnum::FAST_HEALING => FastHealing::class,
        AbilityEnum::HAND_TO_HAND => HandToHand::class,
        AbilityEnum::MACE => Mace::class,
        AbilityEnum::MEDITATION => Meditation::class,
        AbilityEnum::SNEAK => Sneak::class,
        AbilityEnum::SWORD => Sword::class,
        AbilityEnum::WAND => Wand::class,
        AbilityEnum::CURE_LIGHT => CureLight::class,
        AbilityEnum::CURE_POISON => CurePoison::class,
        AbilityEnum::MAGIC_MISSILE => MagicMissile::class,
        AbilityEnum::POISON => Poison::class,
        AbilityEnum::SANCTUARY => Sanctuary::class
    ];

    public static function newInstance(AbilityEnum $ability): Ability
    {
        if (!isset(self::$map[$ability->getValue()])) {
            throw new \UnexpectedValueException(sprintf('unexpected ability: %s', $ability->getValue()));
        }
        return new self::$map[$ability->getValue()];
    }
}
