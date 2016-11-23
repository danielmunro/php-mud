<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Entity\Room;
use PhpMud\Server;
use React\Socket\Connection;

class ServerTest extends CommandTest
{
    public function testHeartbeat()
    {
        global $em;

        $room = new Room();
        $room->setTitle('Test room');
        $server = new Server($em, $room);
        $connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $client = $server->addConnection($connection);
        $client->login('test');
        $client->getMob()->setRoom($room);
        $client->pushBuffer('look');
        static::assertNotEmpty($client->getBuffer());
        $server->heartbeat();
        static::assertEmpty($client->getBuffer());
    }
}
