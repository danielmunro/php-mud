<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Color;
use PhpMud\Command;
use PhpMud\Entity\Direction;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;
use function Functional\map;

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
                        $input->getMob()->getRace()->getVisibilityRequirement()) {
                        return new Output("You can't see a thing!");
                    }

                    return new Output(
                        sprintf(
                            "%s\n%s\n\n[%s: %s]%s%s\n",
                            Color::cyan($room->getTitle()),
                            $room->getDescription(),
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
                                        sprintf("%s\n%s %s", $reduction, $mob->getName(), $mob->getLook())
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
