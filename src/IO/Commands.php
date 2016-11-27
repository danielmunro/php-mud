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

namespace PhpMud\IO;

use PhpMud\Command;
use PhpMud\Server;
use PhpMud\ServiceProvider\Command\DropCommand;
use PhpMud\ServiceProvider\Command\GetCommand;
use PhpMud\ServiceProvider\Command\GossipCommand;
use PhpMud\ServiceProvider\Command\KillCommand;
use PhpMud\ServiceProvider\Command\LookCommand;
use PhpMud\ServiceProvider\Command\MoveCommand;
use PhpMud\ServiceProvider\Command\NewMobCommand;
use PhpMud\ServiceProvider\Command\NewRoomCommand;
use PhpMud\ServiceProvider\Command\QuitCommand;
use PhpMud\ServiceProvider\Command\SleepCommand;
use PhpMud\ServiceProvider\Command\WakeCommand;
use Pimple\Container;
use function Functional\first;

class Commands
{
    /** @var Container $container */
    protected $container;

    /** @var Server $server */
    protected $server;

    public function __construct(Server $server)
    {
        $this->server = $server;

        $this->container = new Container();
        $this->container->register(new MoveCommand());
        $this->container->register(new LookCommand());
        $this->container->register(new NewRoomCommand());
        $this->container->register(new NewMobCommand());
        $this->container->register(new QuitCommand());
        $this->container->register(new GossipCommand());
        $this->container->register(new DropCommand());
        $this->container->register(new GetCommand());
        $this->container->register(new KillCommand());
        $this->container->register(new SleepCommand());
        $this->container->register(new WakeCommand());
    }

    public function execute(Input $input): Output
    {
        return $this->parse($input)->execute($this->server, $input);
    }

    /**
     * @param Input $input
     *
     * @return Command
     */
    private function parse(Input $input): Command
    {
        $command = first($this->container->keys(), function ($key) use ($input) {
            return strpos($key, $input->getCommand()) === 0;
        });

        if ($command) {
            return $this->container[$command]();
        }

        return new class implements Command {
            /**
             * {@inheritdoc}
             */
            public function execute(Server $server, Input $input): Output
            {
                return new Output('What was that?');
            }
        };
    }
}
