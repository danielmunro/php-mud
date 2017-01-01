<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Affect;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;

class AffectsCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['affects'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output(
                        sprintf(
                            "You are affected by:\n%s",
                            reduce_left(
                                $input->getMob()->getAffects()->toArray(),
                                function (Affect $affect, int $index, array $collection, string $reduction) {
                                    return sprintf(
                                        "%s\n%s     %d ticks left",
                                        $reduction,
                                        $affect->getName(),
                                        $affect->getTimeout()
                                    );
                                },
                                ''
                            )
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
