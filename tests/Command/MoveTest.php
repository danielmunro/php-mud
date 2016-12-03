<?php
declare(strict_types=1);

namespace PhpMud\Tests\Command;

use PhpMud\Direction\Down;
use PhpMud\Direction\East;
use PhpMud\Direction\North;
use PhpMud\Direction\South;
use PhpMud\Direction\Up;
use PhpMud\Direction\West;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\Direction\Direction as AbstractDirection;
use PhpMud\Race\Human;
use PhpMud\ServiceProvider\Command\MoveCommand;

class MoveTest extends CommandTest
{
    /**
     * @dataProvider moveDataProvider
     *
     * @param AbstractDirection $direction
     */
    public function testMove(AbstractDirection $direction)
    {
        /**
         * Setup two rooms linked together by the direction passed in
         */
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection($direction, $room2);

        $client = $this->getMockClient();
        $room1->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room1);

        $commands = $this->getCommands();
        $commands->execute($client->input((string)$direction));
        static::assertEquals($room2, $client->getMob()->getRoom());

        $reverse = $direction->reverse();
        $commands->execute($client->input((string)$reverse));
        static::assertEquals($room1, $client->getMob()->getRoom());

        $output = $commands->execute($client->input((string)$reverse));
        static::assertEquals($room1, $client->getMob()->getRoom());
        static::assertEquals(MoveCommand::DIRECTION_NOT_FOUND, $output->getResponse());
    }

    public function moveDataProvider()
    {
        return [
            [
                new North()
            ],
            [
                new South()
            ],
            [
                new East()
            ],
            [
                new West()
            ],
            [
                new Up()
            ],
            [
                new Down()
            ]
        ];
    }

    public function testDispositionFail()
    {
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection(new North(), $room2);

        $client = $this->getMockClient();
        $room1->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room1);

        $commands = $this->getCommands();
        $commands->execute($client->input('sit'));
        $commands->execute($client->input('north'));
        static::assertEquals($room1, $client->getMob()->getRoom());
        $commands->execute($client->input('wake'));
        $commands->execute($client->input('north'));
        static::assertEquals($room2, $client->getMob()->getRoom());
    }

    public function testFightingFail()
    {
        $room1 = new Room();
        $room2 = new Room();
        $room1->addRoomInDirection(new North(), $room2);

        $client = $this->getMockClient();
        $room1->getMobs()->add($client->getMob());
        $client->getMob()->setRoom($room1);
        $room1->getMobs()->add(new Mob('mob', new Human()));

        $commands = $this->getCommands();
        $commands->execute($client->input('kill mob'));
        $commands->execute($client->input('n'));
        static::assertEquals($room1, $client->getMob()->getRoom());
    }
}
