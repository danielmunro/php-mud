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

namespace PhpMud;

use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
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

                    $target = first(
                        $input->getRoom()->getMobs()->toArray(),
                        function (Mob $mob) use ($input) {
                            return $input->isSubjectMatch($mob);
                        }
                    );

                    if (!$target) {
                        return new Output("They aren't here.");
                    }

                    $attacker->setFight(new Fight($attacker, $target));

                    return new Output('You scream and attack!');
                }
            };
        });
    }
}