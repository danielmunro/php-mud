<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\IO\Input;
use React\Socket\Connection;

class MoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param DirectionEnum $direction
     */
    public function testMove(DirectionEnum $direction)
    {
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $connection = $this
            ->getMockBuilder(Connection::class)
            ->disableOriginalConstructor()
            ->getMock();

        $client = new Client($connection);
        $client->pushBuffer('mobName');
        $client->readBuffer();

        $room1->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room1);

        $client->pushBuffer($direction->getValue());
        $client->readBuffer();

        static::assertEquals($room2, $client->getMob()->getRoom());

        $reverse = $direction->reverse();
        $client->pushBuffer($reverse->getValue());
        $client->readBuffer();
        static::assertEquals($room1, $client->getMob()->getRoom());

        $client->pushBuffer($reverse->getValue());
        $client->readBuffer();
        static::assertEquals($room1, $client->getMob()->getRoom());
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
