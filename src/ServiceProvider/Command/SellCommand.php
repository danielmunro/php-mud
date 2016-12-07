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

class SellCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['sell'] = $pimple->protect(function () {
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
                                    $input->getMob()->getInventory()->getItems(),
                                    function (Item $item) use ($input) {
                                        return $input->isSubjectMatch($item);
                                    }
                                ),
                                function (Item $item) use ($input, $shopkeeper) {
                                    if ($item->getValue() > $shopkeeper->getInventory()->getValue()) {
                                        return new Output(
                                            sprintf(
                                                "%s can't afford %s.",
                                                $shopkeeper->getName(),
                                                $item->getName()
                                            )
                                        );
                                    }

                                    $shopkeeper->getInventory()->purchase($item);

                                    return new Output(sprintf('You sell %s.', $item->getName()));
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
