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
use PhpMud\ServiceProvider\Command\LookCommand;
use PhpMud\ServiceProvider\Command\MoveCommand;
use PhpMud\ServiceProvider\Command\NewMobCommand;
use PhpMud\ServiceProvider\Command\NewRoomCommand;
use PhpMud\ServiceProvider\Command\QuitCommand;
use Pimple\Container;
use function Functional\first;

class Commands
{
    /** @var Container $commandContainer */
    protected $commands;

    public function __construct()
    {
        $this->commands = new Container();
        $this->commands->register(new MoveCommand());
        $this->commands->register(new LookCommand());
        $this->commands->register(new NewRoomCommand());
        $this->commands->register(new NewMobCommand());
        $this->commands->register(new QuitCommand());
        $this->commands->register(new GossipCommand());
        $this->commands->register(new DropCommand());
        $this->commands->register(new GetCommand());
    }

    public function execute(Server $server, Input $input): Output
    {
        return $this->parse($input)->execute($server, $input);
    }

    /**
     * @param Input $input
     *
     * @return Command
     */
    private function parse(Input $input): Command
    {
        $command = first($this->commands->keys(), function ($key) use ($input) {
            return strpos($key, $input->getCommand()) === 0;
        });

        if ($command) {
            return $this->commands[$command]();
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
