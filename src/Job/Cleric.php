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
use PhpMud\Skill\Mace;

class Cleric extends Job
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'wis' => 2,
            'int' => 1,
            'dex' => -2,
            'str' => -1,
            'cha' => 2
        ]);

        $this->defaultWeapon = new Mace();
    }

    public function __toString(): string
    {
        return Job::CLERIC;
    }
}
