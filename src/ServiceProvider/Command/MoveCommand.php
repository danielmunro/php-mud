<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Direction as DirectionEntity;
use PhpMud\Enum\Direction;
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
                $mob = $input->getMob();
                $targetDirection = first(
                    $mob->getRoom()->getDirections()->toArray(),
                    function (DirectionEntity $d) {
                        return $d->getDirection()->equals($this->direction);
                    }
                );
                if (!$targetDirection) {
                    return new Output(MoveCommand::DIRECTION_NOT_FOUND);
                }
                $mob->setRoom($targetDirection->getTargetRoom());

                return new Output((string) $mob->getRoom());
            }
        };
    }

    public function register(Container $pimple)
    {
        $pimple['down'] = $pimple->protect(function () {
            return static::svc(Direction::DOWN());
        });
        $pimple['d'] = $pimple['down'];

        $pimple['up'] = $pimple->protect(function () {
            return static::svc(Direction::UP());
        });
        $pimple['u'] = $pimple['up'];

        $pimple['north'] = $pimple->protect(function () {
            return static::svc(Direction::NORTH());
        });
        $pimple['n'] = $pimple['north'];

        $pimple['south'] = $pimple->protect(function () {
            return static::svc(Direction::SOUTH());
        });
        $pimple['s'] = $pimple['south'];

        $pimple['east'] = $pimple->protect(function () {
            return static::svc(Direction::EAST());
        });
        $pimple['e'] = $pimple['east'];

        $pimple['west'] = $pimple->protect(function () {
            return static::svc(Direction::WEST());
        });
        $pimple['w'] = $pimple['west'];
    }
}
