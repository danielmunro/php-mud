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
                    if (!$input->getMob()->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

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

                        return new Output(sprintf('you pick up %s off the ground.', $item->getName()));
                    }

                    return new Output("you can't find it.");
                }
            };
        });
    }
}
