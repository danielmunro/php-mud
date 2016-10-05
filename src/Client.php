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
use PhpMud\Command\Down;
use PhpMud\Command\East;
use PhpMud\Command\Huh;
use PhpMud\Command\Look;
use PhpMud\Command\NewRoom;
use PhpMud\Command\North;
use PhpMud\Command\Quit;
use PhpMud\Command\Room;
use PhpMud\Command\South;
use PhpMud\Command\Up;
use PhpMud\Command\West;
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
    protected static $commands = [
        'look' => Look::class,
        'north' => North::class,
        'south' => South::class,
        'east' => East::class,
        'west' => West::class,
        'up' => Up::class,
        'down' => Down::class,
        'new room' => NewRoom::class,
        'quit' => Quit::class,
        'room' => Room::class
    ];

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

    /**
     * @param string $input
     *
     * @return Command
     */
    protected function parseCommand(string $input): Command
    {
        $command = first(static::$commands, function ($class, $command) use ($input) {
            return strpos($command, $input) === 0 || strpos($input, $command) === 0;
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
