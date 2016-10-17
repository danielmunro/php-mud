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
    const DEFAULT_HP = 20;

    const DEFAULT_MP = 100;

    const DEFAULT_MV = 100;

    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @ManyToOne(targetEntity="Room", inversedBy="mobs") */
    protected $room;

    /** @Column(type="integer") */
    protected $hp;

    /** @Column(type="integer") */
    protected $mp;

    /** @Column(type="integer") */
    protected $mv;

    public function __construct(string $name)
    {
        $this->name = $name;
        $this->hp = static::DEFAULT_HP;
        $this->mp = static::DEFAULT_MP;
        $this->mv = static::DEFAULT_MV;
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

    public function getHp(): int
    {
        return $this->hp;
    }

    public function getMp(): int
    {
        return $this->mp;
    }

    public function getMv(): int
    {
        return $this->mv;
    }
}
