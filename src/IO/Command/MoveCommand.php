<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Direction\Down;
use PhpMud\Direction\East;
use PhpMud\Direction\North;
use PhpMud\Direction\South;
use PhpMud\Direction\Up;
use PhpMud\Direction\West;
use PhpMud\Entity\Direction as DirectionEntity;
use PhpMud\Direction\Direction;
use PhpMud\Enum\Disposition;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;

class MoveCommand implements ServiceProviderInterface
{
    public static function svc(Direction $direction): Command
    {
        return new class($direction) implements Command
        {
            /** @var Direction $direction */
            protected $direction;

            /**
             * @param Direction $direction
             */
            public function __construct(Direction $direction)
            {
                $this->direction = $direction;
            }

            /**
             * {@inheritdoc}
             */
            public function execute(Server $server, Input $input): Output
            {
                return MoveCommand::move($input, $this->direction) ??
                    $server->getCommands()->execute(new Input('look', $input->getClient()));
            }

            public function getRequiredAccessLevel(): AccessLevel
            {
                return AccessLevel::MOB();
            }
        };
    }

    public static function move(Input $input, Direction $direction)
    {
        if (!$input->getDisposition()->equals(Disposition::STANDING())) {
            return $input->getClient()->getDispositionCheckFail();
        }

        if ($input->getMob()->getFight()) {
            return new Output('You are fighting!');
        }

        $mob = $input->getMob();
        $targetDirection = first(
            $mob->getRoom()->getDirections()->toArray(),
            function (DirectionEntity $d) use ($direction) {
                return (string)$d->getDirection() === (string)$direction;
            }
        );

        if (!$targetDirection) {
            return new Output('Alas, that direction does not exist.');
        }

        $mob->getRoom()->notify($mob, new Output(sprintf("%s leaves heading %s.\n", (string)$mob, (string)$targetDirection)));
        $mob->setRoom($targetDirection->getTargetRoom());
        $mob->getRoom()->notify($mob, new Output(sprintf("%s arrives.\n", (string)$mob)));
    }

    public function register(Container $pimple)
    {
        $pimple['down'] = $pimple->protect(function () {
            return static::svc(new Down());
        });

        $pimple['up'] = $pimple->protect(function () {
            return static::svc(new Up());
        });

        $pimple['north'] = $pimple->protect(function () {
            return static::svc(new North());
        });

        $pimple['south'] = $pimple->protect(function () {
            return static::svc(new South());
        });

        $pimple['east'] = $pimple->protect(function () {
            return static::svc(new East());
        });

        $pimple['west'] = $pimple->protect(function () {
            return static::svc(new West());
        });
    }
}
