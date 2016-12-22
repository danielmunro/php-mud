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

namespace PhpMud\Ability;

use PhpMud\Enum\Disposition;
use PhpMud\Enum\TargetType;
use PhpMud\Job\Job;

interface Ability
{
    public function getMinimumDisposition(): Disposition;

    public function getTargetType(): TargetType;

    public function getLevel(Job $job): int;

    public function __toString(): string;
}
