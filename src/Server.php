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
use PhpMud\IO\Input;
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

    /** @var EntityManager $em */
    protected $em;

    /**
     * @param LoopInterface $loop
     * @param EntityManager $em
     * @param Room $startRoom
     */
    public function __construct(
        LoopInterface $loop,
        EntityManager $em,
        Room $startRoom
    ) {
        $this->clients = new ArrayCollection();
        $this->loop = $loop;
        $this->em = $em;
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
        $client = new Client($connection);
        $this->clients->add($client);
        $client->getMob()->setRoom($this->startRoom);
        $this->startRoom->getMobs()->add($client->getMob());
        $client->write((new Look())->execute(new Input($client->getMob()))->getOutput());

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

    }

    /**
     * Updates in longer intervals
     */
    public function tick()
    {
        $this->em->persist($this->startRoom);
        $this->em->flush();

        $this->loop->addTimer(
            random_int(self::TICK_MIN_SECONDS, self::TICK_MAX_SECONDS),
            function() {
                $this->tick();
            }
        );
    }
}