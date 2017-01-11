<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\with;
use function Functional\first;

class VanquishCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['vanquish'] = $pimple->protect(function () {
            return new class implements Command
            {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    return with($input->getRoomMob(), function (Mob $mob) use ($server) {
                        $server->vanquish($mob);

                        return new Output(
                            sprintf(
                                'You vanquish %s!',
                                (string)$mob
                            )
                        );
                    }) ?? new Output("You can't find them.");
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
