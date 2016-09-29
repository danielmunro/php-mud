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

    /** @OneToMany(targetEntity="Mob", mappedBy="room") */
    protected $mobs;

    /** @OneToMany(targetEntity="Direction", mappedBy="sourceRoom") */
    protected $directions;

    public function __construct()
    {
        $this->mobs = new ArrayCollection();
        $this->directions = new ArrayCollection();
        $this->title = '';
        $this->description = '';
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getMobs(): ArrayCollection
    {
        return $this->mobs;
    }

    public function getDirections(): ArrayCollection
    {
        return $this->directions;
    }

    public function __toString(): string
    {
        return $this->title."\n".$this->description."\n".'Exits ['.array_reduce($this->directions->toArray(), function($dirs, Direction $d) {
            return $dirs . substr($d->getDirection(), 0, 1);
        }).'] ';
    }
}