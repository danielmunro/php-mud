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
use PhpMud\Entity\Ability;
use PhpMud\Performable;
use PhpMud\Server;
use PhpMud\IO\Command\BuyCommand;
use PhpMud\IO\Command\DropCommand;
use PhpMud\IO\Command\EquippedCommand;
use PhpMud\IO\Command\GetCommand;
use PhpMud\IO\Command\GossipCommand;
use PhpMud\IO\Command\HelpCommand;
use PhpMud\IO\Command\InventoryCommand;
use PhpMud\IO\Command\KillCommand;
use PhpMud\IO\Command\LevelCommand;
use PhpMud\IO\Command\ListCommand;
use PhpMud\IO\Command\LookCommand;
use PhpMud\IO\Command\MoveCommand;
use PhpMud\IO\Command\NewMobCommand;
use PhpMud\IO\Command\NewRoomCommand;
use PhpMud\IO\Command\QuitCommand;
use PhpMud\IO\Command\RemoveCommand;
use PhpMud\IO\Command\ScoreCommand;
use PhpMud\IO\Command\SellCommand;
use PhpMud\IO\Command\SitCommand;
use PhpMud\IO\Command\SkillsCommand;
use PhpMud\IO\Command\SleepCommand;
use PhpMud\IO\Command\TimeCommand;
use PhpMud\IO\Command\WakeCommand;
use PhpMud\IO\Command\WearCommand;
use PhpMud\IO\Command\WeatherCommand;
use Pimple\Container;
use function Functional\first;
use function Functional\with;

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
        $this->container->register(new SitCommand());
        $this->container->register(new TimeCommand());
        $this->container->register(new WeatherCommand());
        $this->container->register(new WearCommand());
        $this->container->register(new RemoveCommand());
        $this->container->register(new EquippedCommand());
        $this->container->register(new InventoryCommand());
        $this->container->register(new ScoreCommand());
        $this->container->register(new ListCommand());
        $this->container->register(new BuyCommand());
        $this->container->register(new SellCommand());
        $this->container->register(new HelpCommand());
        $this->container->register(new LevelCommand());
        $this->container->register(new SkillsCommand());
    }

    public function execute(Input $input): Output
    {
        return
            $input->getCommand() ?
            $this->parse($input)->execute($this->server, $input) :
            new class implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output('');
                }
            };
    }

    /**
     * @param Input $input
     *
     * @return Command
     */
    private function parse(Input $input): Command
    {
        return
            /**
             * First check for a command
             */
            with(
                first($this->container->keys(), function ($key) use ($input) {
                    return strpos($key, $input->getCommand()) === 0;
                }),
                function (string $command) {
                    return $this->container[$command]();
                }
            ) ??

            /**
             * Next check for a performable ability
             */
            with(
                first(
                    $input->getMob()->getAbilities()->toArray(),
                    function (Ability $ability) use ($input) {
                        return $ability->getAbility() instanceof Performable &&
                            $input->isAbilityMatch($ability);
                    }
                ),
                function (Ability $ability) use ($input) {
                    if (!$input->getMob()->getDisposition()->satisfiesMinimumDisposition(
                        $ability->getAbility()->getMinimumDisposition()
                    ))
                    {
                        return $this->dispositionCheckFailCommand();
                    }

                    return new class($ability) implements Command {

                        /** @var Ability $ability */
                        protected $ability;

                        public function __construct($ability)
                        {
                            $this->ability = $ability;
                        }

                        public function execute(Server $server, Input $input): Output
                        {
                            return $this->ability->getAbility()->perform($input);
                        }
                    };
                }
            ) ??

            /**
             * Finally, give up
             */
            $this->unknownInputCommand();
    }

    private function unknownInputCommand(): Command
    {
        return new class implements Command {
            public function execute(Server $server, Input $input): Output
            {
                return new Output('What was that?');
            }
        };
    }

    private function dispositionCheckFailCommand(): Command
    {
        return new class implements Command {
            public function execute(Server $server, Input $input): Output
            {
                return new Output(
                    sprintf(
                        'No way! You are %s.',
                        $input->getMob()->getDisposition()
                    )
                );
            }
        };
    }
}
