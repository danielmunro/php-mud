<?php
declare(strict_types=1);

namespace PhpMud\Tests\Entity;

use PhpMud\Entity\Inventory;
use PhpMud\Entity\Item;
use PhpMud\Enum\Material;
use PhpMud\Fixture\ItemFixture;

class InventoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @param int $value
     * @param int $initialGold
     * @param int $initialSilver
     * @param int $finalGold
     * @param int $finalSilver
     *
     * @dataProvider purchaseDataProvider
     */
    public function testPurchase(int $value, int $initialGold, int $initialSilver, int $finalGold, int $finalSilver)
    {
        $inventory1 = new Inventory();
        $inventory2 = new Inventory();
        $inventory2->modifyGold($initialGold);
        $inventory2->modifySilver($initialSilver);
        $item = (new ItemFixture(
            new Item(
                'a test item',
                Material::WOOD(),
                ['test', 'item']
            )
        ))
            ->setValue($value)
            ->getInstance();

        $inventory1->add($item);
        $inventory2->purchase($item);

        static::assertEquals($finalGold, $inventory2->getGold());
        static::assertEquals($finalSilver, $inventory2->getSilver());
    }

    public function purchaseDataProvider()
    {
        return [
            [
                10, 1, 0, 0, 990
            ],
            [
                1, 0, 1, 0, 0
            ],
            [
                100, 1, 0, 0, 900
            ],
            [
                1001, 1, 10, 0, 9
            ],
            [
                143, 0, 150, 0, 7
            ],
            [
                1, 1, 1, 1, 0
            ]
        ];
    }
}
