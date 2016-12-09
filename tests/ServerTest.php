<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Client;
use PhpMud\Entity\Area;
use PhpMud\Entity\Room;
use PhpMud\Server;
use React\Socket\Connection;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testHeartbeat()
    {
        $server = $this->getMockServer();
        $client = $this->getMockClient($server);
        $client->pushBuffer('look');
        static::assertNotEmpty($client->getBuffer());
        $server->heartbeat();
        static::assertEmpty($client->getBuffer());
    }

    protected function getMockClient(Server $server): Client
    {
        $connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $client = $server->addConnection($connection);
        $client->login('test');
        $client->login('human');
        $client->login('warrior');
        $client->login('neutral');
        $client->getMob()->setRoom($server->getStartRoom());

        return $client;
    }

    protected function getMockServer(): Server
    {
        global $em;

        $area = new Area('test');
        $room = new Room();
        $room->setTitle('Test room');
        $area->addRoom($room);

        return new Server($em, $room);
    }
}
