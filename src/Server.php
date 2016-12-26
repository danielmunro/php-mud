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

/**
 * Mud server
 */
class Server
{
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

    /** @var array $mobs */
    protected static $mobs = [];

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
                if ($login->next(new Input($input, $client)) === Login::STATE_COMPLETE) {
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
                self::addMob($mob);
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
                    'ip' => $client->getConnection()->getRemoteAddress()
                ]);
                $client->getConnection()->close();
                $this->clients->removeElement($client);

                return;
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
        invoke(static::$mobs, 'pulse');
        each(
            $this->clients,
            function (Client $client) {
                if (!$client->getMob()) {
                    return;
                }
                with(
                    $client->getMob()->getFight(),
                    function (Fight $fight) use ($client) {

                        $client->write(
                            sprintf(
                                "%s %s.\n%s",
                                $fight->getTarget()->getName(),
                                $fight->getTarget()->getCondition(),
                                $client->prompt()
                            )
                        );
                    }
                );
            }
        );
    }

    /**
     * Updates in longer intervals
     */
    public function tick()
    {
        $this->logger->info('tick', [
            'clients' => $this->clients->count(),
            'mobs' => count(static::$mobs)
        ]);

        $this->time->incrementTime();

        each(
            static::$mobs,
            function (Mob $mob) {
                $mob->regen();
                $mob->decrementAffects();
            }
        );

        each(
            $this->clients->toArray(),
            function (Client $client) {
                with($client->getMob(), function () use ($client) {
                    $client->write("\n" . $client->prompt());
                });
            }
        );

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

    public function getClients(): ArrayCollection
    {
        return $this->clients;
    }

    public static function addMob(Mob $mob)
    {
        static::$mobs[] = $mob;
    }

    public static function removeMob(Mob $mob)
    {
        static::$mobs = filter(
            static::$mobs,
            function (Mob $m) use ($mob) {
                return $m->getId() !== $mob->getId();
            }
        );
    }
}
