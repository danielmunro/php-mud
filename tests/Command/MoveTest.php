<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\ServiceProvider\Command\MoveCommand;

class MoveTest extends CommandTest
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param DirectionEnum $direction
     */
    public function testMove(DirectionEnum $direction)
    {
        /**
         * Setup two rooms linked together by the direction passed in
         */
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $client = $this->getMockClient();
        $room1->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room1);

        $commands = $this->getCommands();
        $commands->execute($client->input($direction->getValue()));
        static::assertEquals($room2, $client->getMob()->getRoom());

        $reverse = $direction->reverse();
        $commands->execute($client->input($reverse->getValue()));
        static::assertEquals($room1, $client->getMob()->getRoom());

        $output = $commands->execute($client->input($reverse->getValue()));
        static::assertEquals($room1, $client->getMob()->getRoom());
        static::assertEquals(MoveCommand::DIRECTION_NOT_FOUND, $output->getResponse());
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
