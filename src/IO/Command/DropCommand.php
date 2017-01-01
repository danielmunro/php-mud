<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;

class DropCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['drop'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    $item = first(
                        $input->getMob()->getInventory()->getItems(),
                        function (Item $item) use ($input) {
                            return $input->isSubjectMatch($item);
                        }
                    );

                    if ($item) {
                        $input->getMob()->getInventory()->remove($item);
                        $input->getRoom()->getInventory()->add($item);
                        $item->setInventory($input->getMob()->getInventory());

                        return new Output(sprintf('you drop %s.', $item->getName()));
                    }

                    return new Output("you can't find it.");
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
