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
    const DEFAULT_CAPACITY_WEIGHT = 500;
    const DEFAULT_CAPACITY_COUNT = 250;

    use PrimaryKeyTrait;

    /** @OneToMany(targetEntity="Item", mappedBy="inventory", cascade={"persist"}) */
    protected $items;

    /** @Column(type="integer") */
    protected $gold;

    /** @Column(type="integer") */
    protected $silver;

    /** @Column(type="integer") */
    protected $capacityWeight;

    /** @Column(type="integer") */
    protected $capacityCount;

    public function __construct(
        int $capacityWeight = self::DEFAULT_CAPACITY_WEIGHT,
        int $capacityCount = self::DEFAULT_CAPACITY_COUNT
    ) {
        $this->items = new ArrayCollection();
        $this->capacityWeight = $capacityWeight;
        $this->capacityCount = $capacityCount;
        $this->gold = 0;
        $this->silver = 0;
    }

    public function getItems(): array
    {
        return $this->items->toArray();
    }

    public function add(Item $item)
    {
        $this->items->add($item);
        $item->setInventory($this);
    }

    public function remove(Item $item)
    {
        $this->items->removeElement($item);
    }

    public function getGold(): int
    {
        return $this->gold;
    }

    public function getSilver(): int
    {
        return $this->silver;
    }

    public function getValue(): int
    {
        return ($this->gold * 1000) + $this->silver;
    }

    public function getWeight(): float
    {
        return reduce_left(
            $this->items->toArray(),
            function (Item $item, int $index, array $collection, float $reduction) {
                return $reduction + $item->getWeight();
            },
            0.0
        );
    }

    public function getCapacityWeight(): int
    {
        return $this->capacityWeight;
    }

    public function getCapacityCount(): int
    {
        return $this->capacityCount;
    }

    public function __toString(): string
    {
        return reduce_left(
            $this->items->toArray(),
            function (Item $item, $index, $collection, $reduction) {
                return $reduction . $item->getName() . " is here.\n";
            },
            "\n"
        ).'';
    }
}
