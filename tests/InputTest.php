<?php
declare(strict_types=1);

class InputTest extends PHPUnit_Framework_TestCase
{
    public function testInput()
    {
        $room1 = new \PhpMud\Entity\Room();
        $room2 = new \PhpMud\Entity\Room();
        $direction = new \PhpMud\Entity\Direction($room1, \PhpMud\Enum\Direction::NORTH(), $room2);
        $room1->getDirections()->add($direction);

        $mob = new \PhpMud\Entity\Mob('test mob');
        $room1->getMobs()->add($mob);
        $mob->setRoom($room1);

        $command = new \PhpMud\Command\North();
        $command->execute(new \PhpMud\Input($mob, ['north']));

        static::assertEquals($mob->getRoom(), $room2);
    }
}