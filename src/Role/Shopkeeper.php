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
use PhpMud\Enum\Role as RoleEnum;

class Shopkeeper implements Role
{
    public function perform(Mob $mob)
    {
    }

    public function __toString(): string
    {
        return RoleEnum::SHOPKEEPER;
    }
}
