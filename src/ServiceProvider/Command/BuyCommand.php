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

class BuyCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['buy'] = $pimple->protect(function () {
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

                    $shopItem = first(
                        $shopkeeper->getShopInventory()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    /** @var Item $itemToSell */
                    if ($shopItem) {
                        $itemToSell = clone $shopItem;
                    } else {
                        $itemToSell = first(
                            $shopkeeper->getInventory()->getItems(),
                            function (Item $item) use ($input) {
                                return $input->isSubjectMatch($item);
                            }
                        );

                        if (!$itemToSell) {
                            return new Output(sprintf("%s says, \"I don't have that.\"", $shopkeeper->getName()));
                        }
                    }

                    return new Output(sprintf("Here's a smashing deal on %s.", $itemToSell->getName()));
                }
            };
        });
    }
}
