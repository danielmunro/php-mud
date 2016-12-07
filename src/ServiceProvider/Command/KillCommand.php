<?php
declare(strict_types=1);

/**
 * This file is part of the PhpMud package.
 *
 * (c) Dan Munro <dan@danmunro.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Mob;
use PhpMud\Enum\Role;
use PhpMud\Fight;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\first;

/**
 * Kill
 */
class KillCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['kill'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    $attacker = $input->getMob();

                    if ($attacker->getFight()) {
                        return new Output("No way! You're already fighting.");
                    }

                    if (!$input->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    /** @var Mob $target */
                    $target = first(
                        $input->getRoom()->getMobs()->toArray(),
                        function (Mob $mob) use ($input) {
                            return $input->isSubjectMatch($mob);
                        }
                    );

                    if (!$target) {
                        return new Output("They aren't here.");
                    }

                    if ($target->hasRole(Role::SHOPKEEPER())) {
                        return new Output(sprintf("%s wouldn't like that very much.", $target->getName()));
                    }

                    $attacker->setFight(new Fight($attacker, $target));

                    return new Output('You scream and attack!');
                }
            };
        });
    }
}
