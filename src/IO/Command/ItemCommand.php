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
use function Functional\with;
use function Functional\first;
use function Functional\last;

class ItemCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['item'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return with(
                        first(
                            $input->getMob()->getInventory()->getItems(),
                            function (Item $item) use ($input) {
                                return $input->isSubjectMatch($item);
                            }
                        ),
                        function (Item $item) use ($input) {
                            switch ($input->getOption()) {
                                case 'value':
                                    $item->setValue((float)last($input->getArgs()));
                                    return new Output(
                                        sprintf(
                                            "%s's value becomes %d.",
                                            (string)$item,
                                            $item->getValue()
                                        )
                                    );
                                case '':
                                default:
                                    return new Output('TBD');
                            }
                        }
                    );

                }
            };
        });
    }
}
