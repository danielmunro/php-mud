<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Entity\Area;
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
                        case 'area':
                            $areaName = strtolower($input->getAssigningValue());
                            return with(
                                first(
                                    $server->getAreas(),
                                    function (Area $area) use ($areaName) {
                                        return strtolower($area->getName()) === $areaName;
                                    }
                                ),
                                function (Area $area) use ($input) {
                                    $input->getRoom()->setArea($area);

                                    return new Output('Room area updated.');
                                }
                            ) ?? new Output('Unknown area.');
                        case 'title':
                            $input->getRoom()->setTitle($input->getAssigningValue());

                            return new Output('Room title updated.');
                        case 'description':
                            $input->getRoom()->setDescription($input->getAssigningValue());

                            return new Output('Room description updated.');
                        case 'regen':
                            $input->getRoom()->setRegenRate((float)$input->getAssigningValue());

                            return new Output('Room regen rate updated.');
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
                                    "(%d) %s\n%s\nRegen rate: %f",
                                    $input->getRoom()->getId(),
                                    $input->getRoom()->getTitle(),
                                    (string)$input->getArea(),
                                    $input->getRoom()->getRegenRate()
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
