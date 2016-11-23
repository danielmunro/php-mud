<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;

class LookCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['look'] = $pimple->protect(function () {
            return new class implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output((string) $input->getRoom().reduce_left(
                        $input->getRoom()->getMobs()->toArray(),
                        function (Mob $mob, $index, $collection, $reduction) use ($input) {
                            return $mob === $input->getMob() ? '' : $reduction."\n".$mob->getName();
                        },
                        "\n"
                    ));
                }
            };
        });
    }
}
