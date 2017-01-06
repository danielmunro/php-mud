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

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\AsCommand;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Ability;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Role;
use PhpMud\Enum\TargetType;
use PhpMud\Fight;
use PhpMud\IO\Command\AffectsCommand;
use PhpMud\IO\Command\AreaCommand;
use PhpMud\IO\Command\CraftCommand;
use PhpMud\IO\Command\GiveCommand;
use PhpMud\IO\Command\ItemCommand;
use PhpMud\IO\Command\MobCommand;
use PhpMud\IO\Command\QuietCommand;
use PhpMud\IO\Command\RoleCommand;
use PhpMud\IO\Command\RoomCommand;
use PhpMud\IO\Command\SummonCommand;
use PhpMud\IO\Command\VanquishCommand;
use PhpMud\Ability\Performable;
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
use PhpMud\IO\Command\BuildCommand;
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
        $this->container->register(new BuildCommand());
        $this->container->register(new MobCommand());
        $this->container->register(new SummonCommand());
        $this->container->register(new VanquishCommand());
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
        $this->container->register(new AffectsCommand());
        $this->container->register(new RoomCommand());
        $this->container->register(new RoleCommand());
        $this->container->register(new AreaCommand());
        $this->container->register(new QuietCommand());
        $this->container->register(new CraftCommand());
        $this->container->register(new ItemCommand());
        $this->container->register(new GiveCommand());
        $this->container->register(new AsCommand());
    }

    public function execute(Input $input): Output
    {
        try {
            return
                $input->getCommand() ?
                    $this->parse($input)->execute($this->server, $input) :
                    new Output('');
        } catch (\InvalidArgumentException $e) {
            return new Output($e->getMessage());
        }
    }

    private function parse(Input $input): Command
    {
        /** @var string $commandName */
        $commandName = first($this->container->keys(), function ($key) use ($input) {
            return strpos($key, $input->getCommand()) === 0;
        });

        if ($commandName) {
            /** @var Command $command */
            $command = $this->container[$commandName]();
            if ($input->getClient()->getMob()->getAccessLevel()->satisfies($command->getRequiredAccessLevel())) {
                return $command;
            }

            throw new \InvalidArgumentException("You don't have access to that.");
        }

        return
            with(
                first(
                    $input->getMob()->getAbilities()->toArray(),
                    function (Ability $ability) use ($input) {
                        return $ability->getAbility() instanceof Performable &&
                            $input->isAbilityMatch($ability);
                    }
                ),
                function (Ability $ability) use ($input) {
                    return $this->getAbilityCommand($input, $ability);
                }
            ) ??

            $this->unknownInputCommand();
    }

    private function getAbilityCommand(Input $input, Ability $ability): Command
    {
        if (!$input->getMob()->getDisposition()->satisfiesMinimumDisposition(
            $ability->getAbility()->getMinimumDisposition()
        ))
        {
            return $this->dispositionCheckFailCommand();
        }

        $target = null;

        if ($input->getSubject()) {
            $target = first(
                $input->getRoom()->getMobs()->toArray(),
                function (Mob $mob) use ($input) {
                    return $input->isSubjectMatch($mob);
                }
            );

            if (!$target) {
                return $this->targetNotFoundCommand();
            }
        }

        if (!$ability->getAbility()->canPerform($input->getMob())) {
            return $this->tooTiredCommand();
        }

        if ($ability->getAbility()->getTargetType()->equals(TargetType::OFFENSIVE())) {
            if ($target && $target->hasRole(Role::SHOPKEEPER())) {
                return $this->noAttackingShopkeeperCommand();
            }

            if ($target && $input->getTarget() && $input->getTarget() !== $target) {
                return $this->tooManyTargetsCommand();
            } elseif ($target && !$input->getMob()->getFight()) {
                $input->getMob()->setFight(new Fight($input->getMob(), $target));
            }

            if (!$input->getMob()->getFight()) {
                return $this->noTargetCommand();
            }
        }

        $input->getMob()->incrementDelay($ability->getAbility()->getDelay());
        $ability->getAbility()->applySuccessCost($input->getMob());

        return static::command(function (Input $input) use ($ability) {
            return $ability->getAbility()->perform($input);
        });
    }

    private function tooTiredCommand(): Command
    {
        return static::command(function () {
            return new Output('You are too tired.');
        });
    }

    private function noAttackingShopkeeperCommand(): Command
    {
        return static::command(function () {
            return new Output("No way! They wouldn't like that.");
        });
    }

    private function noTargetCommand(): Command
    {
        return static::command(function () {
            return new Output("You're not fighting anyone.");
        });
    }

    private function targetNotFoundCommand(): Command
    {
        return static::command(function () {
            return new Output("You don't see them here.");
        });
    }

    private function tooManyTargetsCommand(): Command
    {
        return static::command(function (Input $input) {
            return new Output(sprintf("You're already fighting %s!", $input->getTarget()));
        });
    }

    private function unknownInputCommand(): Command
    {
        return static::command(function () {
            return new Output('What was that?');
        });
    }

    private function dispositionCheckFailCommand(): Command
    {
        return static::command(function (Input $input) {
            return new Output(sprintf('No way! You are %s.', $input->getMob()->getDisposition()));
        });
    }

    private static function command(callable $callback): Command
    {
        return new class($callback) implements Command {

            protected $callback;

            public function __construct(callable $callback)
            {
                $this->callback = $callback;
            }

            public function execute(Server $server, Input $input): Output
            {
                return ($this->callback)($input);
            }

            public function getRequiredAccessLevel(): AccessLevel
            {
                return AccessLevel::MOB();
            }
        };
    }
}
