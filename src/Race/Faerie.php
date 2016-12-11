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

class Faerie extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'hp' => 20,
            'mana' => 100,
            'mv' => 100,
            'str' => 11,
            'int' => 18,
            'wis' => 18,
            'dex' => 17,
            'con' => 11,
            'cha' => 17,
            'hit' => 1,
            'dam' => 1,
            'acSlash' => 0,
            'acBash' => 0,
            'acPierce' => 0,
            'acMagic' => 10
        ]);
        $this->visibilityRequirement = 20;
        $this->size = Size::XSMALL();
        $this->creationPoints = 13;
    }

    public function __toString(): string
    {
        return Race::ELF;
    }
}