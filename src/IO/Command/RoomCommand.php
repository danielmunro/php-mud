<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Entity\Direction;
use PhpMud\Enum\AccessLevel;
use PhpMud\Server;
use PhpMud\Entity\Room;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\last;
use function Functional\with;
use function Functional\first;

class RoomCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['room'] = $pimple->protect(function () {

            return new class implements Command {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    switch ($input->getSubject()) {
                        case 'title':
                            $input->getRoom()->setTitle($input->getAssigningValue());
                            return new Output('Room title updated.');
                        case 'description':
                            $input->getRoom()->setDescription($input->getAssigningValue());
                            return new Output('Room description updated.');
                        case 'link':
                            // @todo handle existing directions
                            $direction = \PhpMud\Direction\Direction::fromValue(
                                \PhpMud\Direction\Direction::matchPartialValue($input->getOption())
                            );

                            return with(
                                $server->getRoom((int)last($input->getArgs())),
                                function (Room $room) use ($direction, $input) {
                                    $input->getRoom()->addRoomInDirection(
                                        $direction,
                                        $room
                                    );
                                }
                            ) ?? new Output('Rooms linked.');
                        case null:
                            return new Output(
                                sprintf(
                                    "%s\nArea: %s\n, ID: %d",
                                    $input->getRoom()->getTitle(),
                                    (string)$input->getRoom()->getArea(),
                                    $input->getRoom()->getId()
                                )
                            );
                        default:
                            return new Output('Options are: title, description, link');
                    }
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
