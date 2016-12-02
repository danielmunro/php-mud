<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
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
    const DIRECTION_NOT_FOUND = 'Alas, that direction does not exist';

    protected static function svc(Direction $direction): Command
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
                if (!$input->getDisposition()->canInteract()
                    || $input->getDisposition()->equals(Disposition::SITTING())
                ) {
                    return $input->getClient()->getDispositionCheckFail();
                }

                $mob = $input->getMob();
                $targetDirection = first(
                    $mob->getRoom()->getDirections()->toArray(),
                    function (DirectionEntity $d) {
                        return (string)$d->getDirection() === (string)$this->direction;
                    }
                );

                if (!$targetDirection) {
                    return new Output(MoveCommand::DIRECTION_NOT_FOUND);
                }

                if (!$mob->getDisposition()->equals(Disposition::STANDING())) {
                    return new Output(sprintf('No way! You are %s.', $mob->getDisposition()->getValue()));
                }

                $mob->setRoom($targetDirection->getTargetRoom());

                return $server->getCommands()->execute(new Input($input->getClient(), 'look'));
            }
        };
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
