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

use PhpMud\Entity\Area;

class AreaFixture extends Fixture
{
    protected $area;

    public function __construct(Area $area)
    {
        $this->area = $area;
    }

    public function addRoom(RoomFixture $room): self
    {
        $this->area->addRoom($room->getInstance());

        return $this;
    }

    public function getInstance(): Area
    {
        return $this->area;
    }
}
