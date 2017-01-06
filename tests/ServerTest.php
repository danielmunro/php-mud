<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Client;
use PhpMud\Entity\Area;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Fixture\MobFixture;
use PhpMud\Race\Human;
use PhpMud\Server;
use React\Socket\Connection;

class ServerTest extends \PHPUnit_Framework_TestCase
{
    public function testHeartbeat()
    {
        $server = $this->getMockServer();
        $this->getMockClient($server);
        static::assertCount(1, $server->getClients());
        $server->heartbeat();
        static::assertCount(0, $server->getClients());
    }

    protected function getMockClient(Server $server): Client
    {
        $client = $server->addConnection(
            $this->getMockBuilder(Connection::class)->disableOriginalConstructor()->getMock()
        );
        $client->setMob((new MobFixture(new Mob('mob', new Human())))->modifySilver(20)->getInstance());
        $client->getMob()->setRoom($server->getStartRoom());

        return $client;
    }

    protected function getMockServer(): Server
    {
        global $em, $log;

        $area = new Area('test');
        $room = new Room();
        $room->setTitle('Test room');
        $area->addRoom($room);

        return new Server($em, $room, $log);
    }
}
