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
use PhpMud\Skill\Dagger;
use PhpMud\Skill\Weapon;

class Thief implements Job
{
    public function getStartingAttributes(): Attributes
    {
        return new Attributes([
            'wis' => -2,
            'int' => -1,
            'dex' => 2,
            'str' => 1,
            'cha' => 1
        ]);
    }

    public function getDefaultWeapon(): Weapon
    {
        return new Dagger();
    }

    public function __toString(): string
    {
        return Job::THIEF;
    }
}
