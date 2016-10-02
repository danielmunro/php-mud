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

namespace PhpMud\IO;

use Doctrine\Common\Collections\ArrayCollection;
use PhpMud\Entity\Mob;

class Input
{
    /**
     * @var Mob $mob
     */
    protected $mob;

    /**
     * @var ArrayCollection $args
     */
    protected $args;

    public function __construct(Mob $mob, array $args = [])
    {
        $this->mob = $mob;
        $this->args = new ArrayCollection($args);
    }

    public function getMob(): Mob
    {
        return $this->mob;
    }

    public function getArgs(): ArrayCollection
    {
        return $this->args;
    }
}
