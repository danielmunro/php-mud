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

use PhpMud\Command\Huh;
use PhpMud\Command\Look;
use PhpMud\Command\North;
use PhpMud\Command\South;
use PhpMud\Entity\Mob;
use React\Socket\Connection;
use function Functional\first;

/**
 * A client
 */
class Client
{
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
    protected $user;

    /**
     * @var Login
     */
    protected $login;

    /**
     * @var array
     */
    protected static $commands = [
        'look' => Look::class,
        'north' => North::class,
        'south' => South::class
    ];

    /**
     * Client constructor.
     *
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
        //$this->login = new Login();
        //if (!$this->user) {
            //$this->login

        //}
        $this->user = new Mob('mymob');
    }

    /**
     * @param string $buffer
     */
    public function pushBuffer(string $buffer): void
    {
        $this->buffer[] = $buffer;
    }

    /**
     * @param string $output
     */
    public function write(string $output): void
    {
        $this->connection->write($output);
    }

    /**
     * The main application loop
     */
    public function heartbeat(): void
    {
        if ($this->canReadBuffer()) {
            $args = explode(' ', trim(array_shift($this->buffer)));
            $commandName = $args[0];
            $className = static::parseCommand($commandName);
            /** @var Command $command */
            $command = new $className($this);
            $output = $command->execute(new Input($this->user, $args));
            $this->write($output->getOutput());
        }
    }

    /**
     * @return Mob
     */
    public function getUser(): Mob
    {
        return $this->user;
    }

    /**
     * @param string $input
     *
     * @return string
     */
    private static function parseCommand(string $input): string
    {
        $command = first(static::$commands, function($class, $command) use ($input) {
            return strpos($command, $input) === 0;
        });

        if ($command) {
            return $command;
        }

        return Huh::class;
    }

    /**
     * @return bool
     */
    private function canReadBuffer(): bool
    {
        return !$this->delay && $this->buffer;
    }
}