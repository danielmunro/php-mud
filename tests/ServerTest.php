<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Entity\Room;
use PhpMud\Server;
use React\Socket\Connection;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testHeartbeat()
    {
        $server = $this->getMockServer();
        $connection = $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock();
        $client = $server->addConnection($connection);
        $client->login('test');
        $client->login('human');
        $client->getMob()->setRoom($server->getStartRoom());
        $client->pushBuffer('look');
        static::assertNotEmpty($client->getBuffer());
        $server->heartbeat();
        static::assertEmpty($client->getBuffer());
    }

    protected function getMockServer(): Server
    {
        global $em;

        $room = new Room();
        $room->setTitle('Test room');

        return new Server($em, $room);
    }
}
