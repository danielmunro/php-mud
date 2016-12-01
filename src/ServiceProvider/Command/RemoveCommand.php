<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Item;
use PhpMud\Enum\Position;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;

class RemoveCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['remove'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    /** @var Item $item */
                    $item = first(
                        $input->getMob()->getEquipped()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if (!$item) {
                        return new Output("You can't find it.");
                    }

                    $mob = $input->getMob();
                    $mob->getEquipped()->remove($item);
                    $mob->getInventory()->add($item);

                    return new Output(
                        sprintf(
                            'You remove %s and put it in your inventory.',
                            $item->getName()
                        )
                    );
                }
            };
        });
    }
}
