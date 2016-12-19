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
use PhpMud\Skill\Weapon;

interface Job
{
    const CLERIC = 'cleric';
    const MAGE = 'mage';
    const THIEF = 'thief';
    const WARRIOR = 'warrior';
    const UNINITIATED = 'uninitiated';

    public function getDefaultWeapon(): Weapon;

    public function getStartingAttributes(): Attributes;

    public function __toString(): string;
}
