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

use PhpMud\Entity\Room;
use PhpMud\Repository\Mob;

/**
 *
 */
class Login
{
    /**
     * @var
     */
    protected $state;

    /**
     * @var Room
     */
    protected $startRoom;

    /**
     * @var Mob
     */
    protected $mobRepository;

    /**
     * Login constructor.
     * @param Room $startRoom
     * @param Mob $mobRepository
     */
    public function __construct(Room $startRoom, Mob $mobRepository)
    {
        $this->startRoom = $startRoom;
        $this->mobRepository = $mobRepository;
    }
}
