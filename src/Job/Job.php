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
use PhpMud\Enum\Ability;

interface Job
{
    public function getDefaultWeapon(): Ability;

    public function getStartingAttributes(): Attributes;

    public function getRandomHpGain(): int;

    public function getRandomManaGain(): int;

    public function getRandomMvGain(): int;

    public function __toString(): string;
}
