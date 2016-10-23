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

use PhpMud\Enum\Material;

/**
 * @Entity
 */
class Item
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @Column(type="string") */
    protected $material;

    /** @Column(type="decimal") */
    protected $weight;

    /** @Column(type="decimal") */
    protected $value;

    /** @ManyToOne(targetEntity="Inventory", inversedBy="items") */
    protected $inventory;

    public function __construct(string $name, Material $material, float $weight = 0.0, float $value = 0.0)
    {
        $this->name = $name;
        $this->material = $material;
        $this->weight = $weight;
        $this->value = $value;
    }

    public function setInventory(Inventory $inventory)
    {
        $this->inventory = $inventory;
    }

    public function getName(): string
    {
        return $this->name;
    }
}