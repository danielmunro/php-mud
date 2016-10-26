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

use PhpMud\Enum\Direction as DirectionEnum;

/**
 * Class Direction
 * @package PhpMud\Entity
 * @Entity
 */
class Direction
{
    use PrimaryKeyTrait;

    /** @ManyToOne(targetEntity="Room", inversedBy="directions") */
    protected $sourceRoom;

    /** @ManyToOne(targetEntity="Room", cascade={"persist"}) */
    protected $targetRoom;

    /** @Column(type="string") */
    protected $direction;

    public function __construct(Room $sourceRoom, \PhpMud\Enum\Direction $direction, Room $targetRoom)
    {
        $this->direction = $direction->getValue();
        $this->sourceRoom = $sourceRoom;
        $this->targetRoom = $targetRoom;
    }

    public function getSourceRoom(): Room
    {
        return $this->sourceRoom;
    }

    public function getTargetRoom(): Room
    {
        return $this->targetRoom;
    }

    public function getDirection(): DirectionEnum
    {
        return new DirectionEnum($this->direction);
    }

    public function setTargetRoom(Room $targetRoom)
    {
        $this->targetRoom = $targetRoom;
    }
}
