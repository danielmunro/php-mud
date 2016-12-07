<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Role;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;
use function Functional\with;

class BuyCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['buy'] = $pimple->protect(function () {
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
                        function (Mob $shopkeeper) use ($input) {
                            $output = with(
                                first(
                                    $shopkeeper->getInventory()->getItems(),
                                    function (Item $item) use ($input) {
                                        return $input->isSubjectMatch($item);
                                    }
                                ),
                                function (Item $item) use ($input) {
                                    if ($item->getValue() > $input->getMob()->getInventory()->getValue()) {
                                        return new Output(
                                            sprintf("You can't afford %s.", $item->getName())
                                        );
                                    }

                                    $input->getMob()->getInventory()->purchase($item);

                                    return new Output(sprintf('You purchase %s.', $item->getName()));
                                }
                            );

                            if ($output) {
                                return $output;
                            }

                            return new Output("They don't have that.");
                        }
                    );

                    if ($output) {
                        return $output;
                    }

                    return new Output("They aren't here.");
                }
            };
        });
    }
}
