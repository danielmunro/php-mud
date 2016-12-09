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

namespace PhpMud\Race;

use PhpMud\Entity\Attributes;
use PhpMud\Enum\Size;

class Ogre extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'hp' => 20,
            'mana' => 100,
            'mv' => 100,
            'str' => 18,
            'int' => 12,
            'wis' => 13,
            'dex' => 12,
            'con' => 18,
            'cha' => 11,
            'hit' => 1,
            'dam' => 2,
            'acSlash' => 0,
            'acBash' => 10,
            'acPierce' => 0,
            'acMagic' => -10
        ]);

        $this->visibilityRequirement = 70;

        $this->size = Size::LARGE();
    }

    public function __toString(): string
    {
        return Race::OGRE;
    }
}
