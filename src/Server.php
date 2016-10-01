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
use PhpMud\Command\Look;
use PhpMud\Entity\Room;
use PhpMud\Enum\ServerEvent;
use React\EventLoop\LoopInterface;
use React\Socket\Connection;
use React\Socket\Server as SocketServer;

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

    /** @var Room $startRoom */
    protected $startRoom;

    /** @var EntityManager $em */
    protected $em;

    /**
     * @param EntityManager $em
     * @param Room $startRoom
     */
    public function __construct(EntityManager $em, Room $startRoom)
    {
        $this->clients = new ArrayCollection();
        $this->em = $em;
        $this->startRoom = $startRoom;
    }

    /**
     * @param SocketServer $socket
     * @param int $port
     */
    public function listen(SocketServer $socket, int $port): void
    {
        $socket->on(ServerEvent::CONNECTION, [$this, 'addConnection']);
        $socket->listen($port);
    }

    /**
     * @param Connection $connection
     */
    public function addConnection(Connection $connection): void
    {
        $client = new Client($connection);
        $this->clients->add($client);
        $client->getUser()->setRoom($this->startRoom);
        $this->startRoom->getMobs()->add($client->getUser());
        $client->write((new Look())->execute(new Input($client->getUser()))->getOutput());

        $connection->on(ServerEvent::CLOSE, function() use ($client) {
            $this->clients->removeElement($client);
        });
        $connection->on(ServerEvent::DATA, function(string $input) use ($client) {
            $client->pushBuffer($input);
        });
    }

    /**
     * The main loop
     */
    public function heartbeat(): void
    {
        foreach ($this->clients as $client) {
            /** @var Client $client */
            $client->heartbeat();
        }
    }

    /**
     * Periodic updates
     */
    public function pulse()
    {

    }

    /**
     * @param LoopInterface $loop
     */
    public function tick(LoopInterface $loop)
    {
        $this->em->persist($this->startRoom);
        $this->em->flush();

        $loop->addTimer(random_int(self::TICK_MIN_SECONDS, self::TICK_MAX_SECONDS), function() use ($loop) {
            $this->tick($loop);
        });
    }
}