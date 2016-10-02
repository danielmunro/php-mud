<?php
declare(strict_types=1);

class MoveTest extends PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param \PhpMud\Enum\Direction $direction
     * @param \PhpMud\Command $commandLeave
     * @param \PhpMud\Command $commandReturn
     */
    public function testMove(\PhpMud\Enum\Direction $direction, \PhpMud\Command $commandLeave, \PhpMud\Command $commandReturn)
    {
        $room1 = new \PhpMud\Entity\Room();
        $room2 = new \PhpMud\Entity\Room();
        $direction1 = new \PhpMud\Entity\Direction($room1, $direction, $room2);
        $room1->getDirections()->add($direction1);
        $direction2 = new \PhpMud\Entity\Direction($room2, $direction->reverse(), $room1);
        $room2->getDirections()->add($direction2);

        $mob = new \PhpMud\Entity\Mob('test mob');
        $room1->getMobs()->add($mob);
        $mob->setRoom($room1);
        $input = new \PhpMud\Input($mob);

        $output = $commandLeave->execute($input);

        static::assertEquals(\PhpMud\Enum\CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room2, $mob->getRoom());

        $commandReturn->execute($input);

        static::assertEquals(\PhpMud\Enum\CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room1, $mob->getRoom());

        $output = $commandReturn->execute($input);

        static::assertEquals(\PhpMud\Enum\CommandResult::FAILURE(), $output->getCommandResult());
    }

    public function moveDataProvider()
    {
        return [
            [
                \PhpMud\Enum\Direction::NORTH(),
                new \PhpMud\Command\North(),
                new \PhpMud\Command\South()
            ],
            [
                \PhpMud\Enum\Direction::SOUTH(),
                new \PhpMud\Command\South(),
                new \PhpMud\Command\North()
            ],
            [
                \PhpMud\Enum\Direction::EAST(),
                new \PhpMud\Command\East(),
                new \PhpMud\Command\West()
            ],
            [
                \PhpMud\Enum\Direction::WEST(),
                new \PhpMud\Command\West(),
                new \PhpMud\Command\East()
            ],
            [
                \PhpMud\Enum\Direction::UP(),
                new \PhpMud\Command\Up(),
                new \PhpMud\Command\Down()
            ],
            [
                \PhpMud\Enum\Direction::DOWN(),
                new \PhpMud\Command\Down(),
                new \PhpMud\Command\Up()
            ]
        ];
    }
}