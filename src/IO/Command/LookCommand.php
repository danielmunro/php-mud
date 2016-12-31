<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Color;
use PhpMud\Command;
use PhpMud\Entity\Direction;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Noun;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;
use function Functional\with;
use function Functional\first;

class LookCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['look'] = $pimple->protect(function () {
            return new class implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    $room = $input->getRoom();

                    if (!$input->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    if ($server->getTime()->getVisibility() +
                        $room->getArea()->getWeather()->getVisibility() +
                        $room->getVisibility() <=
                        $input->getMob()->getRace()->getVisibilityRequirement()->getValue()) {
                        return new Output("You can't see a thing!");
                    }

                    if ($input->getSubject()) {
                        return with(
                            first(
                                array_merge(
                                    $room->getMobs()->toArray(),
                                    $room->getInventory()->getItems(),
                                    $input->getMob()->getInventory()->getItems()
                                ),
                                function (Noun $noun) use ($input) {
                                    return $input->isSubjectMatch($noun);
                                }
                            ),
                            function (Noun $noun) {
                                return new Output(sprintf($noun->getLongDescription(), (string)$noun));
                            }
                        ) ?? new Output("You don't see that here.");
                    }

                    return new Output(
                        sprintf(
                            "%s\n  %s\n\n[%s: %s]%s%s\n",
                            Color::cyan($room->getTitle()),
                            wordwrap($room->getDescription()),
                            Color::white('Exits'),
                            Color::green(
                                reduce_left(
                                    $room->getDirections()->toArray(),
                                    function (Direction $direction, $index, $collection, $reduction) {
                                        return sprintf('%s %s', $reduction, (string)$direction);
                                    },
                                    ''
                                )
                            ),
                            reduce_left(
                                $room->getInventory()->getItemsWithQuantity(),
                                function (array $info, string $vNum, array $collection, string $reduction) {
                                    return sprintf(
                                        "%s\n%s",
                                        $reduction,
                                        ($info['count'] > 1 ? '(' . $info['count'] . ') ' : '') .
                                            $info['item']->getName() . ' ' . $info['item']->getLook()
                                    );
                                },
                                ''
                            ),
                            reduce_left(
                                $room->getMobs(),
                                function (Mob $mob, $index, $collection, $reduction) use ($input) {
                                    return $mob !== $input->getMob() ?
                                        sprintf(
                                            "%s\n".$mob->getLook(),
                                            $reduction,
                                            $mob->getName()
                                        )
                                        : $reduction;
                                },
                                ''
                            )
                        )
                    );
                }
            };
        });
    }
}
