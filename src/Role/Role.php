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

namespace PhpMud\Role;

use PhpMud\Entity\Mob;

/**
 * Roles represent small goals that mobs are obliged to accomplish. These goals
 * are designed to be compose-able. For example, a mobile scavenger will walk
 * around an area and clean it up.
 */
interface Role
{
    /**
     * A frequency roll. This roll will happen roughly every second.
     *
     * @return bool
     */
    public function doesWantToPerformRoll(): bool;

    /**
     * With the provided mob attempt to perform the goal of this role.
     *
     * @param Mob $mob
     *
     * @return void
     */
    public function perform(Mob $mob);

    public function __toString(): string;
}
