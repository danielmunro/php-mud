<?php
declare(strict_types=1);

namespace PhpMud\Tests\Command;

use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Material;
use PhpMud\Enum\Role;
use PhpMud\Fixture\ItemFixture;
use PhpMud\Fixture\MobFixture;
use PhpMud\Race\Human;
use PhpMud\Tests\ServerTest;

class BuySellTest extends ServerTest
{
    public function testBuyNoShopkeeper()
    {
        $server = $this->getMockServer();
        $client = $this->getMockClient($server);
        $client->pushBuffer('buy pie');
        $server->heartbeat();
        static::assertEquals(Mob::INITIAL_SILVER, $client->getMob()->getInventory()->getSilver());
    }

    public function testBuyShopkeeper()
    {
        $shop = (new MobFixture(
            new Mob('shopkeeper', new Human())
        ))
            ->addRole(Role::SHOPKEEPER())
            ->addItem(
                (new ItemFixture(new Item('pie', Material::FOOD(), ['pie'])))
                    ->setValue(Mob::INITIAL_SILVER)
                    ->getInstance()
            )
            ->getInstance();
        $server = $this->getMockServer();
        $server->getStartRoom()->getMobs()->add($shop);
        $client = $this->getMockClient($server);
        static::assertEquals(Mob::INITIAL_SILVER, $client->getMob()->getInventory()->getSilver());
        $client->pushBuffer('buy pie');
        $server->checkBuffer($client);
        static::assertEquals(0, $client->getMob()->getInventory()->getSilver());

        $client->pushBuffer('sell pie');
        $server->checkBuffer($client);
        static::assertEquals(Mob::INITIAL_SILVER, $client->getMob()->getInventory()->getSilver());
    }

    public function testBuyShopkeeperNotEnoughSilver()
    {
        $shop = (new MobFixture(
            new Mob('shopkeeper', new Human())
        ))
            ->addRole(Role::SHOPKEEPER())
            ->addItem(
                (new ItemFixture(new Item('pie', Material::FOOD(), ['pie'])))
                    ->setValue(Mob::INITIAL_SILVER + 1)
                    ->getInstance()
            )
            ->getInstance();
        $server = $this->getMockServer();
        $client = $this->getMockClient($server);
        $client->pushBuffer('buy pie');
        $server->getStartRoom()->getMobs()->add($shop);
        $server->heartbeat();
        static::assertEquals(Mob::INITIAL_SILVER, $client->getMob()->getInventory()->getSilver());
    }
}
