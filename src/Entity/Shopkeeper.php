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

use PhpMud\Race\Race;

/**
 * @Entity
 */
class Shopkeeper extends Mob
{
    protected $shopInventory;

    public function __construct($name, Race $race)
    {
        $this->shopInventory = new Inventory();
        parent::__construct($name, $race);
    }

    public function getShopInventory(): Inventory
    {
        return $this->inventory;
    }
}
