<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Entity\Item;
use PhpMud\Entity\Room;
use PhpMud\Enum\Material;

class GetDropTest extends CommandTest
{
    public function testGetDrop()
    {
        $commands = $this->getCommands();
        $room = new Room();
        $item = new Item('item', Material::COPPER(), ['item']);
        $room->getInventory()->add($item);
        $client = $this->getMockClient();
        $room->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room);
        static::assertEmpty($client->getMob()->getInventory()->getItems());

        $commands->execute($client->input('get foo'));
        static::assertContains($item, $room->getInventory()->getItems());
        static::assertEmpty($client->getMob()->getInventory()->getItems());

        $commands->execute($client->input('get item'));
        static::assertContains($item, $client->getMob()->getInventory()->getItems());
        static::assertEmpty($room->getInventory()->getItems());

        $commands->execute($client->input('drop foo'));
        static::assertContains($item, $client->getMob()->getInventory()->getItems());
        static::assertEmpty($room->getInventory()->getItems());

        $commands->execute($client->input('drop item'));
        static::assertContains($item, $room->getInventory()->getItems());
        static::assertEmpty($client->getMob()->getInventory()->getItems());
    }
}