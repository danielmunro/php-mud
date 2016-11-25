<?php
declare(strict_types=1);

namespace PhpMud\Tests\IO;

use PhpMud\IO\Input;
use PhpMud\Tests\Command\CommandTest;

class InputTest extends CommandTest
{
    public function testInput()
    {
        $client = $this->getMockClient();
        $input = new Input($client, 'roomfact w');

        static::assertEquals(
            [
                'roomfact',
                'w'
            ],
            $input->getArgs()
        );

        static::assertEquals($client, $input->getClient());
        static::assertEquals('roomfact', $input->getCommand());
        static::assertEquals($client->getMob(), $input->getMob());
    }
}