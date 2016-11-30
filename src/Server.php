<?php
declare(strict_types=1);

/**
 * This file is part of the PhpMud package.
 *
 * (c) Dan Munro <dan@danmunro.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMud;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use PhpMud\Entity\Area;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\IO\Commands;
use Pimple\Container;
use React\EventLoop\Factory;
use React\Socket\Connection;
use function Functional\each;

/**
 * Mud server
 */
class Server
{
    /**
     * Minimum number of seconds for a tick
     */
    const TICK_MIN_SECONDS = 25;
    /**
     * Maximum number of seconds for a tick
     */
    const TICK_MAX_SECONDS = 50;

    const EVENT_CLOSE = 'close';

    const EVENT_CONNECTION = 'connection';

    const EVENT_LOGIN = 'login';

    const EVENT_TICK = 'tick';

    /** @var ArrayCollection $clients */
    protected $clients;

    /** @var Room $startRoom */
    protected $startRoom;

    /** @var Container $commands */
    protected $commands;

    /** @var Time $time */
    protected $time;

    /**
     * @param EntityManager $em
     * @param Room $startRoom
     */
    public function __construct(EntityManager $em, Room $startRoom)
    {
        $this->em = $em;
        $this->startRoom = $startRoom;
        $this->clients = new ArrayCollection();
        $this->commands = new Commands($this);
        $this->time = new Time();
    }

    /**
     * @param int $port
     */
    public function listen(int $port)
    {
        $loop = Factory::create();
        $socket = new \React\Socket\Server($loop);
        $socket->on(static::EVENT_CONNECTION, [$this, 'addConnection']);
        $socket->listen($port);

        $loop->addPeriodicTimer(0, [$this, 'heartbeat']);
        $loop->addPeriodicTimer(1, [$this, 'pulse']);
        $loop->addPeriodicTimer(30, [$this, 'tick']);

        $loop->run();
    }

    /**
     * @param Connection $connection
     *
     * @return Client
     */
    public function addConnection(Connection $connection): Client
    {
        $client = new Client($connection);
        $this->clients->add($client);

        $connection->on(
            static::EVENT_LOGIN,
            function (Mob $mob) use ($client) {
                $mob->setRoom($this->startRoom);
                $this->startRoom->getMobs()->add($mob);
                $client->pushBuffer('look');
            }
        );

        $connection->on(
            static::EVENT_CLOSE,
            function () use ($client) {
                $this->clients->removeElement($client);
            }
        );

        $connection->write('By what name do you wish to be known? ');

        return $client;
    }

    /**
     * The main loop
     */
    public function heartbeat()
    {
        $this->clients->map(function (Client $client) {
            if ($client->canReadBuffer()) {
                $this->commands->execute($client->readBuffer())->writeResponse($client);
            }
        });
    }

    /**
     * Updates every second
     */
    public function pulse()
    {
        $this->clients->map(function (Client $client) {
            $client->pulse();
        });
    }

    /**
     * Updates in longer intervals
     */
    public function tick()
    {
        $this->time->incrementTime();

        $this->clients->map(function (Client $client) {
            $client->getMob()->regen();
            $client->write("\n".$client->prompt());
        });

        $this->em->persist($this->startRoom);
        $this->em->flush();

        each(
            $this->em->getRepository(Area::class)->findAll(),
            function (Area $area) {
                if (random_int(1, 4) === 1) {
                    $area->setRandomWeather();
                }
            }
        );
    }

    public function getStartRoom(): Room
    {
        return $this->startRoom;
    }

    public function getTime(): Time
    {
        return $this->time;
    }

    public function getCommands(): Commands
    {
        return $this->commands;
    }
}
