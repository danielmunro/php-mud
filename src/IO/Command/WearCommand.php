<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\Enum\Position;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;
use function Functional\filter;
use function Functional\each;

class WearCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['wear'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    /** @var Item $item */
                    $item = first(
                        $input->getMob()->getInventory()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if (!$item) {
                        return new Output("You can't find it.");
                    }

                    /** @var Position $position */
                    $position = $item->getPosition();

                    if (!$position) {
                        return new Output("You can't wear that.");
                    }

                    /** @var Item $equipped */
                    $equipped = first(
                        $input->getMob()->getEquipped()->getItems(),
                        function (Item $item) use ($position) {
                            return $position->equals($item->getPosition());
                        }
                    );

                    $mob = $input->getMob();

                    if ($equipped) {
                        /**
                        each(
                            $equipped,
                            function (Item $equipment) use ($input) {
                                $input->getMob()->getEquipped()->remove($equipment);
                                $input->getMob()->getInventory()->add($equipment);
                            }
                        );
                         */
                        $mob->getEquipped()->remove($equipped);
                        $mob->getInventory()->add($equipped);
                        $mob->getEquipped()->add($item);
                        $mob->getInventory()->remove($item);

                        return new Output(
                            sprintf(
                                'You remove %s and wear %s.',
                                $equipped->getName(),
                                $item->getName()
                            )
                        );
                    }

                    $mob->getEquipped()->add($item);
                    $mob->getInventory()->remove($item);

                    return new Output(
                        sprintf(
                            'You wear %s.',
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
