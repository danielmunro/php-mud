<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Enum\AccessLevel;
use PhpMud\Enum\Role;
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
                    $shopkeeper = $input->getRoomMob(function (Mob $mob) {
                        return $mob->hasRole(Role::SHOPKEEPER());
                    });

                    if (!$shopkeeper) {
                        return new Output("They aren't here.");
                    }

                    /** @var Item $item */
                    $item = first(
                        $shopkeeper->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if (!$item) {
                        return new Output("You don't have that.");
                    }

                    if ($item->getValue() > $input->getMob()->getInventory()->getValue()) {
                        return new Output(
                            sprintf(
                                "You can't afford %s.",
                                $item->getName()
                            )
                        );
                    }

                    if ($item->getCraftedBy() === $shopkeeper) {
                        $shopkeeper->getInventory()->add(clone $item);
                    }

                    $input->getMob()->getInventory()->purchase($item);

                    return new Output(
                        sprintf(
                            'You buy %s.',
                            $item->getName()
                        )
                    );
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
