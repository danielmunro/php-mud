<?php

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

        $client->login('mob');

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