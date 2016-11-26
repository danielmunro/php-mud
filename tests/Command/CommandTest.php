<?php
declare(strict_types=1);

namespace PhpMud\Tests\Command;

use PhpMud\Client;
use PhpMud\Entity\Room;
use PhpMud\IO\Commands;
use PhpMud\Server;
use React\Socket\Connection;

abstract class CommandTest extends \PHPUnit_Framework_TestCase
{
    protected function getMockClient(): Client
    {
        $client = new Client(
            $this
                ->getMockBuilder(Connection::class)
                ->disableOriginalConstructor()
                ->getMock()
        );

        $client->login('test');
        $client->login('human');

        return $client;
    }

    protected function getCommands(): Commands
    {
        return new Commands($this
            ->getMockBuilder(Server::class)
            ->disableOriginalConstructor()
            ->getMock());
    }
}
