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
use function Functional\reduce_left;

/**
 * @Entity
 */
class Inventory
{
    use PrimaryKeyTrait;

    /** @OneToMany(targetEntity="Item", mappedBy="inventory", cascade={"persist"}) */
    protected $items;

    public function __construct()
    {
        $this->items = new ArrayCollection();
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function __toString(): string
    {
        return reduce_left(
            $this->items->toArray(),
            function(Item $item, $index, $collection, $reduction) {
                return $reduction . $item->getName() . " is here.\n";
            }, "\n").'';
    }
}
