<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Command;
use PhpMud\Command\Down;
use PhpMud\Command\East;
use PhpMud\Command\North;
use PhpMud\Command\South;
use PhpMud\Command\Up;
use PhpMud\Command\West;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Enum\CommandResult;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\IO\Input;
use PhpMud\Service\Direction;

class MoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param DirectionEnum $direction
     * @param Command $commandLeave
     * @param Command $commandReturn
     */
    public function testMove(
        DirectionEnum $direction,
        Command $commandLeave,
        Command $commandReturn
    ) {
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $mob = new Mob('test mob');
        $room1->getMobs()->add($mob);
        $mob->setRoom($room1);
        $input1 = new Input($mob, explode(' ', $direction->getValue()));

        $output = $commandLeave->execute($input1);

        static::assertEquals(CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room2, $mob->getRoom());

        $input2 = new Input($mob, explode(' ', $direction->reverse()->getValue()));
        $output = $commandReturn->execute($input2);

        static::assertEquals(CommandResult::SUCCESS(), $output->getCommandResult());
        static::assertEquals($room1, $mob->getRoom());

        $output = $commandReturn->execute($input2);

        static::assertEquals(CommandResult::FAILURE(), $output->getCommandResult());
    }

    public function moveDataProvider()
    {
        $directionService = new Direction();
        return [
            [
                DirectionEnum::NORTH(),
                new North($directionService),
                new South($directionService)
            ],
            [
                DirectionEnum::SOUTH(),
                new South($directionService),
                new North($directionService)
            ],
            [
                DirectionEnum::EAST(),
                new East($directionService),
                new West($directionService)
            ],
            [
                DirectionEnum::WEST(),
                new West($directionService),
                new East($directionService)
            ],
            [
                DirectionEnum::UP(),
                new Up($directionService),
                new Down($directionService)
            ],
            [
                DirectionEnum::DOWN(),
                new Down($directionService),
                new Up($directionService)
            ]
        ];
    }
}
