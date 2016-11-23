<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Client;
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
