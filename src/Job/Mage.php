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

class Mage extends Job
{
    public function __construct()
    {
        $this->startingAttributes = new Attributes([
            'wis' => 2,
            'int' => 2,
            'str' => -1,
            'dex' => -1,
            'con' => -1
        ]);
    }

    public function __toString(): string
    {
        return Job::MAGE;
    }
}