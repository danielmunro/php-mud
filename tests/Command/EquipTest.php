<?php
declare(strict_types=1);

namespace PhpMud\Tests\Command;

use PhpMud\Entity\Item;
use PhpMud\Enum\Material;
use PhpMud\Enum\Position;

class EquipTest extends CommandTest
{
    public function testEquip()
    {
        $client = $this->getMockClient();

        $inv = $client->getMob()->getInventory();
        $inv->add($this->getMockHelmet());
        $inv->add($this->getMockHelmet());
        $eq = $client->getMob()->getEquipped();
        $commands = $this->getCommands();
        static::assertCount(2, $inv->getItems());
        static::assertCount(0, $eq->getItems());
        $commands->execute($client->input('wear helmet'));
        static::assertCount(1, $inv->getItems());
        static::assertCount(1, $eq->getItems());
        $commands->execute($client->input('remove helmet'));
        static::assertCount(2, $inv->getItems());
        static::assertCount(0, $eq->getItems());
        $output = $commands->execute($client->input('remove helmet'));
        static::assertEquals("You can't find it.", $output->getResponse());
    }

    private function getMockHelmet(): Item
    {
        $item = new Item('helmet', Material::COPPER(), ['helmet']);
        $item->setPosition(Position::HEAD());

        return $item;
    }
}
