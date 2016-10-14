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
use PhpMud\Entity\Room;
use Pimple\Container;
use React\EventLoop\Factory;
use React\Socket\Connection;

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

    const EVENT_DATA = 'data';

    const EVENT_CONNECTION = 'connection';

    /** @var ArrayCollection $clients */
    protected $clients;

    /** @var Room $startRoom */
    protected $startRoom;

    /**
     * @param EntityManager $em
     * @param Room $startRoom
     */
    public function __construct(EntityManager $em, Room $startRoom)
    {
        $this->em = $em;
        $this->startRoom = $startRoom;
        $this->clients = new ArrayCollection();
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
        $loop->addPeriodicTimer(30, function () {
            $this->em->persist($this->startRoom);
            $this->em->flush();

            $this->tick();
        });

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
            static::EVENT_CLOSE,
            function () use ($client) {
                $this->clients->removeElement($client);
            }
        );

        $connection->on(
            static::EVENT_DATA,
            function (string $input) use ($client) {
                $client->pushBuffer($input);
            }
        );

        $client->ready($this->startRoom);

        return $client;
    }

    /**
     * The main loop
     */
    public function heartbeat()
    {
        $this->clients->map(function (Client $client) {
            $client->heartbeat();
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
        $this->clients->map(function (Client $client) {
            $client->tick();
        });
    }
}
