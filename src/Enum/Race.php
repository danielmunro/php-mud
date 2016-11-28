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
use PhpMud\Entity\Attributes;

/**
 * @method static HUMAN()
 * @method static ELF()
 * @method static DWARF()
 * @method static OGRE()
 */
class Race extends Enum
{
    const HUMAN = 'human';
    const ELF = 'elf';
    const DWARF = 'dwarf';
    const OGRE = 'ogre';

    public function getStartingAttributes(): Attributes
    {
        switch ($this->value) {
            case self::HUMAN:
                return new Attributes([
                    'hp' => 20,
                    'mana' => 100,
                    'mv' => 100,
                    'str' => 15,
                    'int' => 15,
                    'wis' => 15,
                    'dex' => 15,
                    'con' => 15,
                    'cha' => 15,
                    'hit' => 1,
                    'dam' => 1,
                    'acSlash' => 0,
                    'acBash' => 0,
                    'acPierce' => 0,
                    'acMagic' => 0
                ]);
            case self::ELF:
                return new Attributes([
                    'hp' => 20,
                    'mana' => 100,
                    'mv' => 100,
                    'str' => 12,
                    'int' => 18,
                    'wis' => 16,
                    'dex' => 18,
                    'con' => 11,
                    'cha' => 17,
                    'hit' => 1,
                    'dam' => 1,
                    'acSlash' => 0,
                    'acBash' => 0,
                    'acPierce' => 0,
                    'acMagic' => 10
                ]);
            case self::DWARF:
                return new Attributes([
                    'hp' => 20,
                    'mana' => 100,
                    'mv' => 100,
                    'str' => 18,
                    'int' => 12,
                    'wis' => 17,
                    'dex' => 11,
                    'con' => 18,
                    'cha' => 13,
                    'hit' => 1,
                    'dam' => 2,
                    'acSlash' => 0,
                    'acBash' => 10,
                    'acPierce' => 0,
                    'acMagic' => 0
                ]);
            case self::OGRE:
                return new Attributes([
                    'hp' => 20,
                    'mana' => 100,
                    'mv' => 100,
                    'str' => 19,
                    'int' => 11,
                    'wis' => 12,
                    'dex' => 12,
                    'con' => 19,
                    'cha' => 11,
                    'hit' => 1,
                    'dam' => 2,
                    'acSlash' => 0,
                    'acBash' => 10,
                    'acPierce' => 0,
                    'acMagic' => -10
                ]);
            default:
                throw new \UnexpectedValueException($this->value);
        }
    }

    public function getSize(): Size
    {
        switch ($this->value) {
            case self::HUMAN:
                return Size::MEDIUM();
            case self::ELF:
                return Size::SMALL();
            case self::DWARF:
                return Size::SMALL();
            case self::OGRE:
                return Size::LARGE();
            default:
                throw new \UnexpectedValueException($this->value);
        }
    }
}
