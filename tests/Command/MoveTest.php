<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Service\Direction;

class MoveTest extends \PHPUnit_Framework_TestCase
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
        $input1 = new \PhpMud\IO\Input($mob, explode(' ', $direction->getValue()));

        $output = $commandLeave->execute($input1);

        static::assertEquals(\PhpMud\Enum\CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room2, $mob->getRoom());

        $input2 = new \PhpMud\IO\Input($mob, explode(' ', $direction->reverse()->getValue()));
        $output = $commandReturn->execute($input2);

        static::assertEquals(\PhpMud\Enum\CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room1, $mob->getRoom());

        $output = $commandReturn->execute($input2);

        static::assertEquals(\PhpMud\Enum\CommandResult::FAILURE(), $output->getCommandResult());
    }

    public function moveDataProvider()
    {
        $directionService = new Direction();
        return [
            [
                \PhpMud\Enum\Direction::NORTH(),
                new \PhpMud\Command\North($directionService),
                new \PhpMud\Command\South($directionService)
            ],
            [
                \PhpMud\Enum\Direction::SOUTH(),
                new \PhpMud\Command\South($directionService),
                new \PhpMud\Command\North($directionService)
            ],
            [
                \PhpMud\Enum\Direction::EAST(),
                new \PhpMud\Command\East($directionService),
                new \PhpMud\Command\West($directionService)
            ],
            [
                \PhpMud\Enum\Direction::WEST(),
                new \PhpMud\Command\West($directionService),
                new \PhpMud\Command\East($directionService)
            ],
            [
                \PhpMud\Enum\Direction::UP(),
                new \PhpMud\Command\Up($directionService),
                new \PhpMud\Command\Down($directionService)
            ],
            [
                \PhpMud\Enum\Direction::DOWN(),
                new \PhpMud\Command\Down($directionService),
                new \PhpMud\Command\Up($directionService)
            ]
        ];
    }
}
