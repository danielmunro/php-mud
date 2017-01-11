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
                    $shopkeeper = $input->getRoomMob(function (Mob $mob) {
                        return $mob->hasRole(Role::SHOPKEEPER());
                    });

                    if (!$shopkeeper) {
                        return new Output("They aren't here.");
                    }

                    /** @var Item $item */
                    $item = first(
                        $input->getMob()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if (!$item) {
                        return new Output("You don't have that.");
                    }

                    if ($item->getCraftedBy() === $shopkeeper) {
                        return new Output("They aren't interested in that.");
                    }

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

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
