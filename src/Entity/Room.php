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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Class Room
 * @package PhpMud\Entity
 * @Entity
 */
class Room
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $title;

    /** @Column(type="text") */
    protected $description;

    /** @OneToMany(targetEntity="Mob", mappedBy="room", cascade={"persist"}) */
    protected $mobs;

    /** @OneToMany(targetEntity="Direction", mappedBy="sourceRoom", cascade={"persist"}) */
    protected $directions;

    /**
     * Room constructor.
     */
    public function __construct()
    {
        $this->mobs = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this->title = '';
        $this->description = '';
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * @return Collection
     */
    public function getMobs(): Collection
    {
        return $this->mobs;
    }

    /**
     * @return Collection
     */
    public function getDirections(): Collection
    {
        return $this->directions;
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return
            $this->title."\n".
            $this->description."\n".
            'Exits ['.array_reduce(
                $this->directions->toArray(),
                function ($dirs, Direction $direction) {
                    return $dirs . substr($direction->getDirection(), 0, 1);
                }
            ).'] ';
    }
}
