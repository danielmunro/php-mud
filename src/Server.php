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
use PhpMud\Entity\Area;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use PhpMud\IO\Commands;
use PhpMud\IO\Input;
use Pimple\Container;
use React\EventLoop\Factory;
use React\Socket\Connection;
use function Functional\each;
use function Functional\invoke;
use function Functional\with;
use function Functional\filter;
use function Functional\partial_method;

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

    /** @var Logger $logger */
    protected $logger;

    /** @var Container $commands */
    protected $commands;

    /** @var Time $time */
    protected $time;

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
        $this->commands = new Commands($this);
        $this->time = new Time(10);
    }

    public function listen(string $ip, int $port)
    {
        $this->logger->info('php-mud is up and running', [
            'start' => new \DateTime(),
            'port' => $port
        ]);

        $loop = Factory::create();
        $socket = new \React\Socket\Server($loop);
        $socket->on(static::EVENT_CONNECTION, [$this, 'addConnection']);
        $socket->listen($port, $ip);

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
        $this->logger->info('new remote connection', [
            'ip' => $connection->getRemoteAddress()
        ]);

        $client = new Client($connection);
        $this->clients->add($client);
        $login = new Login($this->em->getRepository(Mob::class));

        $connection->on(
            Client::EVENT_DATA,
            function (string $input) use ($client, $login, $connection) {
                if ($login->next(new Input($client, $input)) === Login::STATE_COMPLETE) {
                    $client->setMob($login->getMob());
                    $connection->emit(Server::EVENT_LOGIN, ['mob' => $login->getMob()]);
                    $connection->removeAllListeners();
                    $connection->on(Client::EVENT_DATA, [$client, 'pushBuffer']);
                }
            }
        );

        $connection->on(
            static::EVENT_LOGIN,
            function (Mob $mob) use ($client, $connection) {
                $this->logger->info('login', [
                    'ip' => $connection->getRemoteAddress(),
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
                $this->logger->info('logout', [
                    'ip' => $connection->getRemoteAddress(),
                    'mob' => $client->getMob()->getName()
                ]);
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
        each($this->clients->toArray(), function (Client $client) {
            if (!$client->getConnection()->isWritable()) {
                $this->logger->info('connection not writable, closing', [
                    'mob' => $client->getMob()->getName()
                ]);
                $client->getConnection()->close();
                $this->clients->removeElement($client);
            }
            $this->checkBuffer($client);
        });
    }

    public function checkBuffer(Client $client)
    {
        with($client->readBufferIfNotDelayed(), function (Input $input) use ($client) {
            $this->commands->execute($input)->writeResponse($client);
        });
    }

    /**
     * Updates every second
     */
    public function pulse()
    {
        invoke($this->clients->toArray(), 'pulse');
    }

    /**
     * Updates in longer intervals
     */
    public function tick()
    {
        $this->logger->info('tick', [
            'clients' => $this->clients->count()
        ]);

        $this->time->incrementTime();

        each(
            $this->clients->toArray(),
            function (Client $client) {
                with($client->getMob(), function () use ($client) {
                    $client->getMob()->regen();
                    $client->write("\n" . $client->prompt());
                });
            }
        );

        $this->em->persist($this->startRoom);
        $this->em->flush();

        each(
            $this->em->getRepository(Area::class)->findAll(),
            function (Area $area) {
                with(random_int(1, 4) === 1, function () use ($area) {
                    $area->setRandomWeather();
                });
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

    public function getClients(): ArrayCollection
    {
        return $this->clients;
    }
}
