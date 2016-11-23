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
use function Functional\first;

class GetCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['get'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    $item = first(
                        $input->getRoom()->getInventory()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if ($item) {
                        $input->getMob()->getInventory()->add($item);
                        $input->getRoom()->getInventory()->remove($item);
                        $item->setInventory($input->getMob()->getInventory());

                        return new Output('you get '.$item->getName().' off the ground.');
                    }

                    return new Output("you can't find it.");
                }
            };
        });
    }
}
