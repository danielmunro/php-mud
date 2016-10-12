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

use Pimple\Container;
use PhpMud\Command\Huh;
use PhpMud\Command\Look;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
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
    protected $mob;

    /**
     * @var Login
     */
    protected $login;

    /**
     * @var array
     */
    protected $args;

    /** @var Container $commandContainer */
    protected $commandContainer;

    /**
     * Client constructor.
     *
     * @param Connection $connection
     * @param Container $commandContainer
     */
    public function __construct(Connection $connection, Container $commandContainer)
    {
        $this->connection = $connection;
        $this->commandContainer = $commandContainer;
    }

    /**
     * @param string $buffer
     */
    public function pushBuffer(string $buffer)
    {
        $this->buffer[] = $buffer;
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
            $input = trim(array_shift($this->buffer));

            $this->write(
                $this
                    ->parseCommand($input)
                    ->execute(new Input($this->mob, explode(' ', $input)))
                    ->getOutput()."\n--> "
            );
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

    /**
     * @param Entity\Room $startRoom
     */
    public function ready(\PhpMud\Entity\Room $startRoom)
    {
        $this->mob = new Mob('mymob');
        $this->mob->setRoom($startRoom);
        $startRoom->getMobs()->add($this->mob);

        $this->write(
            (new Look())->execute(
                new Input($this->mob)
            )->getOutput()
        );
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    /**
     * @param string $input
     *
     * @return Command
     */
    protected function parseCommand(string $input): Command
    {
        $args = explode(' ', $input);
        $this->args = $args;
        $commandName = $args[0];

        $command = first($this->commandContainer->keys(), function ($key) use ($commandName) {
            return strpos($key, $commandName) === 0;
        });

        if ($command) {
            return $this->commandContainer[$command]($this);
        }

        return new Huh($this);
    }

    /**
     * @return bool
     */
    private function canReadBuffer(): bool
    {
        return !$this->delay && $this->buffer;
    }
}
