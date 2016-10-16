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

use PhpMud\Entity\Room;
use PhpMud\IO\Commands;
use Pimple\Container;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use React\Socket\Connection;

/**
 * A client
 */
class Client
{
    const EVENT_DATA = 'data';

    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * @var int
     */
    protected $delay = 0;

    /**
     * @var Mob
     */
    protected $mob;

    /**
     * @var Login
     */
    protected $login;

    /**
     * @var Container
     */
    protected $commands;

    /**
     * Client constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->commands = new Commands();
        $this->login = new Login();

        $connection->on(
            static::EVENT_DATA,
            function (string $input) {
                $this->pushBuffer($input);
            }
        );
    }

    /**
     * @param string $buffer
     */
    public function pushBuffer(string $buffer)
    {
        $this->buffer[] = $buffer;
    }

    /**
     * Get the oldest command from the buffer and evaluate it.
     */
    public function readBuffer()
    {
        $input = new Input($this, trim(array_shift($this->buffer)));

        if ($this->login) {
            $this->login->next($input);
            if ($this->login->getState() === Login::STATE_COMPLETE) {
                $this->mob = $this->login->getMob();
                $this->login = null;
                $this->connection->emit(Server::EVENT_LOGIN, ['mob' => $this->mob]);
            }
            return;
        }

        $this->write(
            $this
                ->commands
                ->parse($input)($this)
                ->execute($input)
                ->getOutput()."\n--> "
        );
    }

    /**
     * @param string $output
     */
    public function write(string $output)
    {
        $this->connection->write($output);
    }

    /**
     * The main application loop
     */
    public function heartbeat()
    {
        if ($this->canReadBuffer()) {
            $this->readBuffer();
        }
    }

    public function pulse()
    {
        if ($this->delay > 0) {
            $this->delay--;
        }
    }

    public function tick()
    {
        $this->connection->write("\n-->");
    }

    /**
     * @return Mob
     */
    public function getMob(): Mob
    {
        return $this->mob;
    }

    /**
     * Close the connection
     */
    public function disconnect()
    {
        $this->connection->close();
    }

    public function gossip(string $message)
    {
        $this->connection->emit(Server::EVENT_GOSSIP, ['message' => $message]);
    }

    /**
     * @return bool
     */
    private function canReadBuffer(): bool
    {
        return !$this->delay && $this->buffer;
    }
}
