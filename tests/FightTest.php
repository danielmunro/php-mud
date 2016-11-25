<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Entity\Mob;
use PhpMud\Enum\Race;
use PhpMud\Fight;

class FightTest extends \PHPUnit_Framework_TestCase
{
    public function testFight()
    {
        $mob1 = new Mob('dwarvie', Race::DWARF());
        $mob2 = new Mob('hoooman', Race::HUMAN());

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