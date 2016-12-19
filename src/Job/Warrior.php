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

namespace PhpMud\Job;

use PhpMud\Entity\Attributes;
use PhpMud\Skill\Sword;
use PhpMud\Skill\Weapon;

class Warrior implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes([
            'wis' => -1,
            'int' => -1,
            'dex' => 1,
            'str' => 2
        ]);
    }

    public function getDefaultWeapon(): Weapon
    {
        return new Sword();
    }

    public function __toString(): string
    {
        return Job::WARRIOR;
    }
}
