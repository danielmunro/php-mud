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

namespace PhpMud;

use PhpMud\Entity\Mob;

class Input
{
    /**
     * @var Mob $mob
     */
    protected $mob;

    /**
     * @var array $args
     */
    protected $args;

    public function __construct(Mob $mob, array $args = [])
    {
        $this->mob = $mob;
        $this->args = $args;
    }

    public function getMob(): Mob
    {
        return $this->mob;
    }

    public function getArgs(): array
    {
        return $this->args;
    }
}