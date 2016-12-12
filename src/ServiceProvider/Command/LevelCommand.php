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
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LevelCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['level'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getMob()->getDebitLevels()) {
                        return new Output("You have no debit levels.\n");
                    }

                    return new Output(
                        sprintf(
                            "You level up! You are now level %d.\n",
                            $input->getMob()->levelUp()
                        )
                    );
                }
            };
        });
    }
}
