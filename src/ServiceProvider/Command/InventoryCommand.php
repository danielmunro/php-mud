<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Item;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;

class InventoryCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['inventory'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output(
                        reduce_left(
                            $input->getMob()->getInventory()->getItems(),
                            function (Item $item, int $index, array $collection, string $reduction) {
                                return $reduction . $item->getName() . "\n";
                            },
                            ''
                        )
                    );
                }
            };
        });
    }
}
