<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

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
                        sprintf(
                            'You are carrying:%s',
                            reduce_left(
                                $input->getMob()->getInventory()->getItemsWithQuantity(),
                                function (array $info, string $vNum, array $collection, string $reduction) {
                                    return sprintf(
                                        "%s\n%s",
                                        $reduction,
                                        ($info['count'] > 1 ?
                                            '(' . $info['count'] . ') ' :
                                            ''
                                        ) . $info['item']->getName()
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
