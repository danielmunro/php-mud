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
use PhpMud\Enum\Skill;
use PhpMud\Enum\Vuln;
use PhpMud\Job\Job;

class Ogre extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'hp' => 20,
            'mana' => 100,
            'mv' => 100,
            'str' => 17,
            'int' => 12,
            'wis' => 13,
            'dex' => 12,
            'con' => 17,
            'cha' => 11,
            'hit' => 1,
            'dam' => 2,
            'acSlash' => 0,
            'acBash' => 10,
            'acPierce' => 0,
            'acMagic' => -10
        ]);
        $this->visibilityRequirement = EyeSight::VERY_POOR();
        $this->size = Size::LARGE();
        $this->creationPoints = 11;
        $this->bonusSkills = [
            Skill::BASH(),
            Skill::FAST_HEALING()
        ];
        $this->vulns = [
            Vuln::DISTRACTION(),
            Vuln::MENTAL()
        ];
        $this->resists = [
            Vuln::DISEASE(),
            Vuln::COLD()
        ];
    }

    public function getJobExpMultiplier(Job $job): int
    {
        switch ($job) {
            case Job::CLERIC:
                return 150;
            case Job::MAGE:
                return 200;
            case Job::THIEF:
                return 150;
            case Job::WARRIOR:
                return 105;
            default:
                return 100;
        }
    }

    public function __toString(): string
    {
        return Race::OGRE;
    }
}
