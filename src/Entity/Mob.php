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

namespace PhpMud\Entity;

/**
 * Class Mob
 * @package PhpMud\Entity
 * @Entity
 */
class Mob
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @ManyToOne(targetEntity="Room", inversedBy="mobs") */
    protected $room;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getRoom(): Room
    {
        return $this->room;
    }

    public function setRoom(Room $room)
    {
        $this->room = $room;
    }
}