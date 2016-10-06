<?php
declare(strict_types=1);

namespace PhpMud\Tests;

use PhpMud\Command;
use PhpMud\Command\Down;
use PhpMud\Command\East;
use PhpMud\Command\North;
use PhpMud\Command\South;
use PhpMud\Command\Up;
use PhpMud\Command\West;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\IO\Input;
use PhpMud\Service\DirectionService;

class MoveTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param DirectionEnum $direction
     * @param string $command1
     * @param string $command2
     */
    public function testMove(
        DirectionEnum $direction,
        string $command1,
        string $command2
    ) {
        $directionService = new DirectionService();
        /** @var Command $command */
        $command = new $command1($directionService);

        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $mob = new Mob('test mob');
        $room1->getMobs()->add($mob);
        $mob->setRoom($room1);
        $input1 = new Input($mob, explode(' ', $direction->getValue()));

        $command->execute($input1);
        static::assertEquals($room2, $mob->getRoom());

        $command = new $command2($directionService);

        $input2 = new Input($mob, explode(' ', $direction->reverse()->getValue()));
        $command->execute($input2);
        static::assertEquals($room1, $mob->getRoom());

        $command->execute($input2);
        static::assertEquals($room1, $mob->getRoom());
    }

    public function moveDataProvider()
    {
        return [
            [
                DirectionEnum::NORTH(),
                North::class,
                South::class
            ],
            [
                DirectionEnum::SOUTH(),
                South::class,
                North::class
            ],
            [
                DirectionEnum::EAST(),
                East::class,
                West::class
            ],
            [
                DirectionEnum::WEST(),
                West::class,
                East::class
            ],
            [
                DirectionEnum::UP(),
                Up::class,
                Down::class
            ],
            [
                DirectionEnum::DOWN(),
                Down::class,
                Up::class
            ]
        ];
    }
}
