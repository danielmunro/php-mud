<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Role;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;
use function Functional\reduce_left;
use function Functional\with;

class ListCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['list'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    $output = with(
                        first(
                            $input->getRoom()->getMobs()->toArray(),
                            function (Mob $mob) {
                                return $mob->hasRole(Role::SHOPKEEPER());
                            }
                        ),
                        function (Mob $shopkeeper) {
                            return new Output(
                                sprintf(
                                    '[Lv Price Qty] Item %s',
                                    reduce_left(
                                        $shopkeeper->getInventory()->getItems(),
                                        function (Item $item, int $index, array $collection, string $reduction) {
                                            return sprintf(
                                                "%s\n[%s %d %s] %s",
                                                $reduction,
                                                ($item->getLevel() < 10 ? ' ' : '').$item->getLevel(),
                                                $item->getValue(),
                                                (
                                                $item->getValue() < 100000 ?
                                                    str_pad(
                                                        '-- ',
                                                        8 - strlen((string)$item->getValue()),
                                                        ' ',
                                                        STR_PAD_LEFT
                                                    ) :
                                                    '-- '
                                                ),
                                                $item->getName()
                                            );
                                        },
                                        ''
                                    )
                                )
                            );
                        }
                    );

                    if (!$output) {
                        return new Output("They aren't here.");
                    }

                    return $output;
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
