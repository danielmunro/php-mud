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

use PhpMud\Bash;
use PhpMud\Berserk;
use PhpMud\Entity\Attributes;
use PhpMud\Enum\Size;
use PhpMud\Job\Job;

class Dwarf extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
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
        $this->visibilityRequirement = 35;
        $this->size = Size::SMALL();
        $this->creationPoints = 9;
        $this->bonusSkills = [
            'berserk',
            'bash'
        ];
    }

    public function getJobExpMultiplier(Job $job): int
    {
        switch ($job) {
            case Job::CLERIC:
                return 100;
            case Job::MAGE:
                return 150;
            case Job::THIEF:
                return 125;
            case Job::WARRIOR:
                return 100;
            default:
                return 100;
        }
    }

    public function __toString(): string
    {
        return Race::DWARF;
    }
}
