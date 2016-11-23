<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Client;
use PhpMud\Entity\Item;
use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\Enum\Material;
use PhpMud\IO\Commands;
use PhpMud\Server;
use PhpMud\ServiceProvider\Command\MoveCommand;
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
        /**
         * Setup two rooms linked together by the direction passed in
         */
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $client = $this->getMockClient();
        $client->login('mobName');
        $room1->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room1);

        $client->pushBuffer($direction->getValue());
        $server = $this->getMockServer();
        $commands = new Commands();
        $commands->execute($server, $client->readBuffer());

        static::assertEquals($room2, $client->getMob()->getRoom());

        $reverse = $direction->reverse();
        $client->pushBuffer($reverse->getValue());
        $commands->execute($server, $client->readBuffer());

        static::assertEquals($room1, $client->getMob()->getRoom());

        $client->pushBuffer($reverse->getValue());
        $output = $commands->execute($server, $client->readBuffer());

        static::assertEquals($room1, $client->getMob()->getRoom());
        static::assertEquals(MoveCommand::DIRECTION_NOT_FOUND, $output->getResponse());
    }

    public function testGetDrop()
    {
        $server = $this->getMockServer();
        $commands = new Commands();
        $room = new Room();
        $item = new Item('item', Material::COPPER(), ['item']);
        $room->getInventory()->add($item);
        $client = $this->getMockClient();
        $client->login('mobName');
        $room->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room);
        static::assertEmpty($client->getMob()->getInventory()->getItems());

        $client->pushBuffer('get item');
        $commands->execute($server, $client->readBuffer());
        static::assertContains($item, $client->getMob()->getInventory()->getItems());
        static::assertEmpty($room->getInventory()->getItems());

        $client->pushBuffer('drop item');
        $commands->execute($server, $client->readBuffer());
        static::assertContains($item, $room->getInventory()->getItems());
        static::assertEmpty($client->getMob()->getInventory()->getItems());
    }

    private function getMockClient(): Client
    {
        return new Client(
            $this
                ->getMockBuilder(Connection::class)
                ->disableOriginalConstructor()
                ->getMock()
        );
    }

    private function getMockServer(): Server
    {
        return $this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock();
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
