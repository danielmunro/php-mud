<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Race\Human;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SummonCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['summon'] = $pimple->protect(function () {
            return new class implements Command
            {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    $mob = new Mob('a fresh mob', new Human());
                    $mob->setRoom($input->getRoom());

                    return new Output('A fresh mob arrives from the mob factory.');
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
