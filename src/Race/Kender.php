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
use PhpMud\Enum\Job;
use PhpMud\Enum\Size;
use PhpMud\Enum\Ability;
use PhpMud\Enum\Vuln;
use PhpMud\Job\Job as JobInterface;

class Kender extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'hp' => 20,
            'mana' => 100,
            'mv' => 100,
            'str' => 15,
            'int' => 14,
            'wis' => 14,
            'dex' => 18,
            'con' => 15,
            'cha' => 13,
            'hit' => 1,
            'dam' => 1,
            'acSlash' => 0,
            'acBash' => 0,
            'acPierce' => 0,
            'acMagic' => 10
        ]);
        $this->visibilityRequirement = EyeSight::GOOD();
        $this->size = Size::SMALL();
        $this->creationPoints = 6;
        $this->bonusSkills = [
            Ability::DODGE()
        ];
        $this->vulns = [
            Vuln::COLD(),
            Vuln::POISON()
        ];
        $this->resists = [
            Vuln::BASH()
        ];
    }

    public function getJobExpMultiplier(JobInterface $job): int
    {
        switch ($job) {
            case Job::CLERIC:
                return 110;
            case Job::MAGE:
                return 110;
            case Job::THIEF:
                return 100;
            case Job::WARRIOR:
                return 130;
            default:
                return 100;
        }
    }

    public function __toString(): string
    {
        return Race::KENDER;
    }
}
