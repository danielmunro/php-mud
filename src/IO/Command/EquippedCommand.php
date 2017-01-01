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
use function Functional\reduce_left;

class EquippedCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['equipped'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output(
                        reduce_left(
                            $input->getMob()->getEquipped()->getItems(),
                            function (Item $item, int $index, array $collection, string $reduction) {
                                return $reduction . (string)$item->getPosition() . ' - ' . $item->getName() . "\n";
                            },
                            ''
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
