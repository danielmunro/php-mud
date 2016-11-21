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
use PhpMud\IO\Output;
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
     * Client constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        $this->login = new Login();

        $connection->on(static::EVENT_DATA, [$this, 'login']);
    }

    /**
     * @param string $buffer
     */
    public function pushBuffer(string $buffer)
    {
        $this->buffer[] = $buffer;
    }

    public function login(string $input)
    {
        if ($this->login->next(new Input($this, $input)) === Login::STATE_COMPLETE) {
            $this->mob = $this->login->getMob();
            $this->login = null;
            $this->connection->emit(Server::EVENT_LOGIN, ['mob' => $this->mob]);
            $this->connection->removeListener(static::EVENT_DATA, [$this, 'login']);
            $this->connection->on(static::EVENT_DATA, [$this, 'pushBuffer']);
        }
    }

    /**
     * Get the oldest command from the buffer and evaluate it.
     */
    public function readBuffer(): Input
    {
        return new Input($this, trim(array_shift($this->buffer)));
    }

    public function prompt()
    {
        return '--> ';
    }

    /**
     * @param string $output
     */
    public function write(string $output)
    {
        $this->connection->write($output);
    }

    public function close()
    {
        $this->connection->close();
    }

    public function pulse()
    {
        if ($this->delay > 0) {
            $this->delay--;
        }
    }

    public function tick()
    {
        $this->connection->write("\n ".$this->prompt());
    }

    /**
     * @return Mob
     */
    public function getMob(): Mob
    {
        return $this->mob;
    }

    /**
     * @return bool
     */
    public function canReadBuffer(): bool
    {
        return !$this->delay && $this->buffer;
    }
}
