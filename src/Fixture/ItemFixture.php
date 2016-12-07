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

namespace PhpMud\Fixture;

use PhpMud\Entity\Item;
use PhpMud\Enum\Position;

class ItemFixture extends Fixture
{
    protected $item;

    public function __construct(Item $item)
    {
        $this->item = $item;
    }

    public function setPosition(Position $position): self
    {
        $this->item->setPosition($position);

        return $this;
    }

    public function setValue(float $value): self
    {
        $this->item->setValue($value);

        return $this;
    }

    public function setVNum(string $vNum): self
    {
        $this->item->setVNum($vNum);

        return $this;
    }

    public function setWeight(float $weight): self
    {
        $this->item->setWeight($weight);

        return $this;
    }

    public function getInstance(): Item
    {
        return $this->item;
    }
}
