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
use League\Container\Container;
use PhpMud\Entity\Room;
use PhpMud\Enum\ServerEvent;
use React\EventLoop\LoopInterface;
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

    /** @var ArrayCollection $clients */
    protected $clients;

    /** @var LoopInterface $loop */
    protected $loop;

    /** @var Room $startRoom */
    protected $startRoom;

    /** @var EntityManager $entityManager */
    protected $entityManager;

    /** @var Container $commandContainer */
    protected $commandContainer;

    /**
     * @param LoopInterface $loop
     * @param EntityManager $entityManager
     * @param Container $commandContainer
     * @param Room $startRoom
     */
    public function __construct(
        LoopInterface $loop,
        EntityManager $entityManager,
        Container $commandContainer,
        Room $startRoom
    ) {
        $this->clients = new ArrayCollection();
        $this->loop = $loop;
        $this->entityManager = $entityManager;
        $this->commandContainer = $commandContainer;
        $this->startRoom = $startRoom;
    }

    /**
     * @param int $port
     */
    public function listen(int $port)
    {
        $socket = new \React\Socket\Server($this->loop);
        $socket->on(ServerEvent::CONNECTION, [$this, 'addConnection']);
        $socket->listen($port);

        $this->loop->addPeriodicTimer(0, [$this, 'heartbeat']);
        $this->loop->addPeriodicTimer(1, [$this, 'pulse']);
        $this->tick();

        $this->loop->run();
    }

    /**
     * @param Connection $connection
     */
    public function addConnection(Connection $connection)
    {
        $client = new Client($connection, $this->commandContainer);
        $this->clients->add($client);
        $client->getMob()->setRoom($this->startRoom);
        $this->startRoom->getMobs()->add($client->getMob());

        $connection->on(ServerEvent::CLOSE, function () use ($client) {
            $this->clients->removeElement($client);
        });
        $connection->on(ServerEvent::DATA, function (string $input) use ($client) {
            $client->pushBuffer($input);
        });

        $client->ready();
    }

    /**
     * The main loop
     */
    public function heartbeat()
    {
        foreach ($this->clients as $client) {
            /** @var Client $client */
            $client->heartbeat();
        }
    }

    /**
     * Updates every second
     */
    public function pulse()
    {
        foreach ($this->clients as $client) {
            /** @var Client $client */
            $client->pulse();
        }
    }

    /**
     * Updates in longer intervals
     */
    public function tick()
    {
        $this->entityManager->persist($this->startRoom);
        $this->entityManager->flush();

        foreach ($this->clients as $client) {
            /** @var Client $client */
            $client->tick();
        }

        $this->loop->addTimer(
            random_int(self::TICK_MIN_SECONDS, self::TICK_MAX_SECONDS),
            function () {
                $this->tick();
            }
        );
    }
}
