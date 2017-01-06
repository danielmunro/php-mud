<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Role;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;
use function Functional\with;

class RoleCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['role'] = $pimple->protect(function () {
            return new class() implements Command
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
                        function (Mob $mob) use ($input) {
                            switch ($input->getArgs()[2] ?? '') {
                                case 'rm':
                                    $mob->removeRole(new Role($input->getArgs()[3]));
                                    return new Output(sprintf('Role removed from %s.', (string)$mob));
                                case 'add':
                                    $mob->addRole(new Role($input->getArgs()[3]));
                                    return new Output(sprintf('Role added to %s.', (string)$mob));
                                case 'list':
                                case '':
                                    return new Output(
                                        sprintf(
                                            "%s's roles: %s.",
                                            (string)$mob,
                                            implode(', ', $mob->getRoles()
                                            )
                                        )
                                    );
                                default:
                                    return new Output('Not understood. Options are: list, rm, add');
                            }
                        }
                    ) ??
                        new Output("You can't find them.");
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
