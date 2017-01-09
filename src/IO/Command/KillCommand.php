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

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\Enum\Disposition;
use PhpMud\IO\Command\Command;
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
                    $target = $input->getRoomMob(function (Mob $mob) use ($input) {
                        return $input->isSubjectMatch($mob);
                    });

                    if (!$target || !$target->isAlive()) {
                        return new Output("They aren't here.");
                    }

                    if ($target->hasRole(Role::SHOPKEEPER())) {
                        return new Output(sprintf("%s wouldn't like that very much.", $target->getName()));
                    }

                    $attacker->setFight(new Fight($attacker, $target));

                    return new Output('You scream and attack!');
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
