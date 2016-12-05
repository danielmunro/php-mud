<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Shopkeeper;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;
use function Functional\reduce_left;

class ListCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['list'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    /** @var Shopkeeper $shopkeeper */
                    $shopkeeper = first(
                        $input->getRoom()->getMobs()->toArray(),
                        function (Mob $mob) {
                            return $mob instanceof Shopkeeper;
                        }
                    );

                    if (!$shopkeeper) {
                        return new Output("They aren't here.");
                    }

                    return new Output(
                        sprintf(
                            '[Lv Price Qty] Item %s',
                            reduce_left(
                                $shopkeeper->getShopInventory()->getItems(),
                                function (Item $item, int $index, array $collection, string $reduction) {
                                    return sprintf(
                                        "%s\n[%s %d %s] %s",
                                        $reduction,
                                        ($item->getLevel() < 10 ? ' ' : '').$item->getLevel(),
                                        $item->getValue(),
                                        (
                                            $item->getValue() < 100000 ?
                                            str_pad('-- ', 8 - strlen((string)$item->getValue()), ' ', STR_PAD_LEFT) :
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
            };
        });
    }
}
