<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\Server;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Direction;
use PhpMud\Entity\Room;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\last;
use function Functional\first;

class BuildCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['build'] = $pimple->protect(function () {

            return new class implements Command {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    $srcRoom = $input->getRoom();
                    $newRoom = new Room();
                    $newRoom->setTitle($input->getRoom()->getTitle());
                    $newRoom->setDescription($input->getRoom()->getDescription());

                    $direction = \PhpMud\Direction\Direction::matchPartialValue(last($input->getArgs()));

                    if (!$direction) {
                        return new Output('That direction does not exist.');
                    }

                    $direction = \PhpMud\Direction\Direction::fromValue($direction);

                    /** @var Direction $existingDirection */
                    $existingDirection = first(
                        $srcRoom->getDirections()->toArray(),
                        function (Direction $dir) use ($direction) {
                            return (string)$dir->getDirection() === (string)$direction;
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
                                return (string)$dir->getDirection()
                                    === (string)$existingDirection->getDirection()->reverse();
                            }
                        );
                        $reverseDirection->setTargetRoom($newRoom);
                    } else {
                        $srcDirection = new Direction($srcRoom, $direction, $newRoom);
                        $srcRoom->getDirections()->add($srcDirection);
                    }

                    $newDirection = new Direction($newRoom, $direction->reverse(), $srcRoom);
                    $newRoom->getDirections()->add($newDirection);
                    $srcRoom->getArea()->addRoom($newRoom);

                    $server->persist();

                    return new Output(sprintf('A room appears %s.', (string)$direction));
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
