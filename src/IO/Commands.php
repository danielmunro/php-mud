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
    }

    /**
     * @param Input $input
     *
     * @return callable
     */
    public function parse(Input $input): callable
    {
        $commandName = $input->getCommand();
        $command = first($this->commands->keys(), function ($key) use ($commandName) {
            return strpos($key, $commandName) === 0;
        });

        if ($command) {
            return $this->commands[$command];
        }

        return function () {
            return new class implements Command {
                /**
                 * @SuppressWarnings(PHPMD.UnusedLocalVariable)
                 * {@inheritdoc}
                 */
                public function execute(Input $input): Output
                {
                    return new Output('What was that?');
                }
            };
        };
    }
}
