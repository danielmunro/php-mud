<?php
declare(strict_types=1);

class MoveTest extends PHPUnit_Framework_TestCase
{
    public function testMove()
    {
        $room1 = new \PhpMud\Entity\Room();
        $room2 = new \PhpMud\Entity\Room();
        $direction1 = new \PhpMud\Entity\Direction($room1, \PhpMud\Enum\Direction::NORTH(), $room2);
        $room1->getDirections()->add($direction1);
        $direction2 = new \PhpMud\Entity\Direction($room2, \PhpMud\Enum\Direction::SOUTH(), $room1);
        $room2->getDirections()->add($direction2);

        $mob = new \PhpMud\Entity\Mob('test mob');
        $room1->getMobs()->add($mob);
        $mob->setRoom($room1);
        $input = new \PhpMud\Input($mob);

        $command = new \PhpMud\Command\North();
        $output = $command->execute($input);

        static::assertEquals(\PhpMud\Enum\CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room2, $mob->getRoom());

        $command = new \PhpMud\Command\South();
        $command->execute($input);

        static::assertEquals(\PhpMud\Enum\CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room1, $mob->getRoom());

        $output = $command->execute($input);

        static::assertEquals(\PhpMud\Enum\CommandResult::FAILURE(), $output->getCommandResult());
    }
}