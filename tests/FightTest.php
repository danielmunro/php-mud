<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Fight;
use PhpMud\Race\Dwarf;
use PhpMud\Race\Human;

class FightTest extends \PHPUnit_Framework_TestCase
{
    public function testFight()
    {
        $room = new Room();
        $mob1 = new Mob('dwarvie', new Dwarf());
        $mob2 = new Mob('hoooman', new Human());
        $mob1->setRoom($room);
        $mob2->setRoom($room);
        $room->getMobs()->add($mob1);
        $room->getMobs()->add($mob2);

        $fight = new Fight($mob1, $mob2);
        $mob1->setFight($fight);
        $mob2->setFight($fight);

        while ($fight->isContinuing()) {
            static::assertNotNull($mob1->getFight());
            static::assertNotNull($mob2->getFight());
            $fight->turn();
        }

        static::assertNull($mob1->getFight());
        static::assertNull($mob2->getFight());

        $hp = $mob2->getAttribute('hp');
        $fight->turn();
        static::assertEquals($hp, $mob2->getAttribute('hp'));
    }
}
