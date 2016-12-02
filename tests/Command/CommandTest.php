<?php
declare(strict_types=1);

namespace PhpMud\Tests\Command;

use PhpMud\Client;
use PhpMud\Entity\Mob;
use PhpMud\IO\Commands;
use PhpMud\IO\Input;
use PhpMud\Race\Human;
use PhpMud\Server;

abstract class CommandTest extends \PHPUnit_Framework_TestCase
{
    protected function getMockClient(): Client
    {
        $client = $this->getMockBuilder(Client::class)->disableOriginalConstructor()->getMock();
        $client->expects($this->any())->method('getMob')->willReturn(new Mob('foo', new Human()));
        $client->expects($this->any())->method('input')->willReturnCallback(function ($arg) use ($client) {
            return new Input($client, $arg);
        });

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
