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
use Pimple\Container;
use PhpMud\Enum\ServerEvent;
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

    /** @var Container $commandContainer */
    protected $commandContainer;

    /**
     * @param Container $commandContainer
     */
    public function __construct(Container $commandContainer)
    {
        $this->clients = new ArrayCollection();
        $this->commandContainer = $commandContainer;
    }

    /**
     * @param Connection $connection
     *
     * @return Client
     */
    public function addConnection(Connection $connection): Client
    {
        $client = new Client($connection, $this->commandContainer);
        $this->clients->add($client);

        $connection->on(
            ServerEvent::CLOSE,
            function () use ($client) {
                $this->clients->removeElement($client);
            }
        );

        $connection->on(
            ServerEvent::DATA,
            function (string $input) use ($client) {
                $client->pushBuffer($input);
            }
        );

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
