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

class Giant extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'hp' => 20,
            'mana' => 100,
            'mv' => 100,
            'str' => 18,
            'int' => 11,
            'wis' => 11,
            'dex' => 12,
            'con' => 18,
            'cha' => 11,
            'hit' => 1,
            'dam' => 2,
            'acSlash' => 0,
            'acBash' => 0,
            'acPierce' => 0,
            'acMagic' => -10
        ]);
        $this->visibilityRequirement = 60;
        $this->size = Size::XLARGE();
        $this->creationPoints = 5;
    }

    public function __toString(): string
    {
        return Race::OGRE;
    }
}
