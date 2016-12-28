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

use PhpMud\Enum\Role as RoleEnum;

class Roles
{
    protected static $roles = [];

    public static function getRole(string $role): Role
    {
        if (!isset(static::$roles[$role])) {
            static::$roles[$role] = (new RoleEnum($role))->getRole();
        }

        return static::$roles[$role];
    }
}
