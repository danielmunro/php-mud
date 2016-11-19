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
use Monolog\Logger;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
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

    const EVENT_GOSSIP = 'message';

    const EVENT_LOGIN = 'login';

    const EVENT_TICK = 'tick';

    /** @var ArrayCollection $clients */
    protected $clients;

    /** @var Room $startRoom */
    protected $startRoom;

    /** @var Logger $logger */
    protected $logger;

    /** @var Channels $channels */
    protected $channels;

    /**
     * @param EntityManager $em
     * @param Room $startRoom
     * @param Logger $logger
     */
    public function __construct(EntityManager $em, Room $startRoom, Logger $logger)
    {
        $this->em = $em;
        $this->startRoom = $startRoom;
        $this->logger = $logger;
        $this->clients = new ArrayCollection();
        $this->channels = new Channels();
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

        $this->logger->debug('server listening', [
            'port' => $port
        ]);

        $loop->run();
    }

    /**
     * @param Connection $connection
     *
     * @return Client
     */
    public function addConnection(Connection $connection): Client
    {
        $this->logger->info('new remote connection', [
            'remoteAddress' => $connection->getRemoteAddress()
        ]);
        $client = new Client($connection, $this->channels);
        $this->clients->add($client);

        $connection->on(
            static::EVENT_LOGIN,
            function (Mob $mob) use ($client, $connection) {
                $this->logger->info('remote connection logged in', [
                    'remoteAddress' => $connection->getRemoteAddress(),
                    'mob' => $mob->getName()
                ]);
                $mob->setRoom($this->startRoom);
                $this->startRoom->getMobs()->add($mob);
                $client->pushBuffer('look');
            }
        );

        $connection->on(
            static::EVENT_CLOSE,
            function () use ($client, $connection) {
                $this->logger->info('remote connection closed', [
                    'remoteAddress' => $connection->getRemoteAddress()
                ]);
                $this->clients->removeElement($client);
            }
        );

        $connection->on(
            static::EVENT_GOSSIP,
            function (string $input) use ($client) {
                each($this->clients, function (Client $c) use ($client, $input) {
                    if ($c === $client) {
                        $client->write('You gossip, "'.$input.'"'."\n");
                    } else {
                        $c->write($client->getMob()->getName().' gossips, "'.$input.'"'."\n");
                    }
                });
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
            $client->heartbeat();
        });
    }

    /**
     * Updates every second
     */
    public function pulse()
    {
        $this->
        $this->clients->map(function (Client $client) {
            $client->pulse();
        });
    }

    /**
     * Updates in longer intervals
     */
    public function tick()
    {
        $this->logger->info('tick');
        $this->clients->map(function (Client $client) {
            $client->tick();
        });

        $this->em->persist($this->startRoom);
        $this->em->flush();
    }
}
