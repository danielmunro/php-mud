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
use PhpMud\Enum\EyeSight;
use PhpMud\Enum\Size;
use PhpMud\Job\Job;

class Human extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
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
        $this->visibilityRequirement = EyeSight::AVERAGE();
        $this->size = Size::MEDIUM();
        $this->creationPoints = 5;
    }

    public function getJobExpMultiplier(Job $job): int
    {
        return 100;
    }

    public function __toString(): string
    {
        return Race::HUMAN;
    }
}
