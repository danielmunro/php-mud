<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Server;
use UnexpectedValueException;
use PhpMud\Command;
use PhpMud\Entity\Direction;
use PhpMud\Entity\Room;
use PhpMud\Enum\Direction as DirectionEnum;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\last;
use function Functional\first;

class NewRoomCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['roomfact'] = $pimple->protect(function () {

            return new class implements Command {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    $mob = $input->getMob();
                    $srcRoom = $mob->getRoom();
                    $newRoom = new Room();
                    $newRoom->setTitle('A swirling mist');
                    $newRoom->setDescription('You are engulfed by a mist.');

                    $direction = DirectionEnum::matchPartialValue(last($input->getArgs()));
                    if (!$direction) {
                        return new Output('That direction does not exist.');
                    }

                    /** @var Direction $existingDirection */
                    $existingDirection = first(
                        $srcRoom->getDirections()->toArray(),
                        function (Direction $dir) use ($direction) {
                            return $dir->getDirection()->equals($direction);
                        }
                    );

                    if ($existingDirection) {
                        $existingRoom = $existingDirection->getTargetRoom();
                        $existingDirection->setTargetRoom($newRoom);
                        $newRoom->getDirections()->add(
                            new Direction(
                                $newRoom,
                                $existingDirection->getDirection(),
                                $existingRoom
                            )
                        );

                        /** @var Direction $reverseDirection */
                        $reverseDirection = first(
                            $existingRoom->getDirections()->toArray(),
                            function (Direction $dir) use ($existingDirection) {
                                return $dir->getDirection()->equals($existingDirection->getDirection()->reverse());
                            }
                        );
                        $reverseDirection->setTargetRoom($newRoom);
                    } else {
                        $srcDirection = new Direction($srcRoom, $direction, $newRoom);
                        $srcRoom->getDirections()->add($srcDirection);
                    }

                    $newDirection = new Direction($newRoom, $direction->reverse(), $srcRoom);
                    $newRoom->getDirections()->add($newDirection);

                    return new Output('A room appears '.$direction->getValue());
                }
            };
        });
    }
}
