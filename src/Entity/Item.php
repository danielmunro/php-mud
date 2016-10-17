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
 * @Entity
 */
class Item
{
    use PrimaryKeyTrait;

    protected $name;

    protected $material;

    protected $weight;

    protected $value;

    /** @ORM\ManyToOne(targetEntity="Inventory") */
    protected $inventory;
}