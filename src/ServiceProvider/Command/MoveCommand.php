<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Enum\Direction;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MoveCommand implements ServiceProviderInterface
{
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
            public function execute(Input $input): Output
            {
                $mob = $input->getMob();
                $sourceRoom = $mob->getRoom();
                $targetDirection = $sourceRoom->getDirections()->filter(function (\PhpMud\Entity\Direction $d) {
                    return strpos($d->getDirection(), $this->direction->getValue()) === 0;
                })->first();
                if (!$targetDirection) {
                    return new Output('Alas, that direction does not exist');
                }
                $sourceRoom->getMobs()->removeElement($mob);
                $mob->setRoom($targetDirection->getTargetRoom());
                $mob->getRoom()->getMobs()->add($mob);

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
