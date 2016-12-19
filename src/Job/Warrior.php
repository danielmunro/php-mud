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

class Warrior extends Job
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'wis' => -1,
            'int' => -1,
            'dex' => 1,
            'str' => 2
        ]);

        $this->defaultWeapon = new Sword();
    }

    public function __toString(): string
    {
        return Job::WARRIOR;
    }
}
