<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Entity\Mob;
use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\with;
use function Functional\first;

class AsCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['as'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return with(
                        first(
                            $input->getRoom()->getMobs()->toArray(),
                            function (Mob $mob) use ($input) {
                                return $input->isSubjectMatch($mob);
                            }
                        ),
                        function (Mob $mob) use ($server, $input) {
                            return $server->getCommands()->execute(
                                new Input(
                                    implode(' ', array_slice($input->getArgs(), 2)),
                                    $input->getClient(),
                                    $mob
                                )
                            );
                        }
                    ) ?? new Output("They aren't here.");
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
