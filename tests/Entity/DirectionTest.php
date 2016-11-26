<?php
declare(strict_types=1);

namespace PhpMud\Tests\Entity;

use PhpMud\Entity\Room;
use PhpMud\Enum\Direction;

class DirectionTest extends \PHPUnit_Framework_TestCase
{
    public function testDirection()
    {
        $room1 = new Room();
        $room2 = new Room();
        $room3 = new Room();

        $room1Direction = $room1->addRoomInDirection(Direction::WEST(), $room2);

        static::assertEquals($room1, $room1Direction->getSourceRoom());
        static::assertEquals($room2, $room1Direction->getTargetRoom());

        $room1Direction->setTargetRoom($room3);

        static::assertEquals($room3, $room1Direction->getTargetRoom());
    }
}
