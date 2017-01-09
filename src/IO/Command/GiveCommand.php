<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;

class GiveCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['give'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    /** @var Item $item */
                    $item = first(
                        $input->getMob()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if (!$item) {
                        return new Output("You don't have that item.");
                    }

                    /** @var Mob $receiver */
                    $receiver = $input->getRoomMob(function (Mob $mob) use ($input) {
                        return $input->isOptionMatch($mob);
                    });

                    if (!$receiver) {
                        return new Output("They aren't here.");
                    }

                    $input->getMob()->getInventory()->remove($item);
                    $receiver->getInventory()->add($item);

                    return new Output(
                        sprintf(
                            'You give %s to %s.',
                            (string)$item,
                            (string)$receiver
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
