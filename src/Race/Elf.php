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
use PhpMud\Enum\Ability;
use PhpMud\Enum\Vuln;
use PhpMud\Enum\Job as JobEnum;
use PhpMud\Job\Job;

class Elf extends Race
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'hp' => 20,
            'mana' => 100,
            'mv' => 100,
            'str' => 12,
            'int' => 17,
            'wis' => 16,
            'dex' => 16,
            'con' => 12,
            'cha' => 17,
            'hit' => 1,
            'dam' => 1,
            'acSlash' => 0,
            'acBash' => 0,
            'acPierce' => 0,
            'acMagic' => 10
        ]);
        $this->visibilityRequirement = EyeSight::GOOD();
        $this->size = Size::SMALL();
        $this->creationPoints = 14;
        $this->bonusSkills = [
            Ability::SNEAK()
        ];
        $this->vulns = [
            Vuln::IRON()
        ];
        $this->resists = [
            Vuln::MENTAL()
        ];
    }

    public function getJobExpMultiplier(Job $job): int
    {
        switch ((string)$job) {
            case JobEnum::CLERIC:
                return 125;
            case JobEnum::MAGE:
                return 100;
            case JobEnum::THIEF:
                return 100;
            case JobEnum::WARRIOR:
                return 120;
            default:
                return 100;
        }
    }

    public function __toString(): string
    {
        return Race::ELF;
    }
}
