<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Command;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\IO\Input;

class MoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param DirectionEnum $direction
     */
    public function testMove(DirectionEnum $direction)
    {
        /** @var Command $command */
        $command = new Command\Move($direction);

        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $mob = new Mob('test mob');
        $room1->getMobs()->add($mob);
        $mob->setRoom($room1);
        $input1 = new Input($mob, explode(' ', $direction->getValue()));

        $command->execute($input1);
        static::assertEquals($room2, $mob->getRoom());

        $command = new Command\Move($direction->reverse());

        $input2 = new Input($mob, explode(' ', $direction->reverse()->getValue()));
        $command->execute($input2);
        static::assertEquals($room1, $mob->getRoom());

        $command->execute($input2);
        static::assertEquals($room1, $mob->getRoom());
    }

    public function moveDataProvider()
    {
        return [
            [
                DirectionEnum::NORTH()
            ],
            [
                DirectionEnum::SOUTH()
            ],
            [
                DirectionEnum::EAST()
            ],
            [
                DirectionEnum::WEST()
            ],
            [
                DirectionEnum::UP()
            ],
            [
                DirectionEnum::DOWN()
            ]
        ];
    }
}
