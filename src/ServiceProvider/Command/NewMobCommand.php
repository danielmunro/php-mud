<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Race\Human;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class NewMobCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['mobfact'] = $pimple->protect(function () {
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

                    $input->getRoom()->getMobs()->add($mob);
                    $mob->setRoom($input->getRoom());

                    return new Output('A fresh mob arrives from the mob factory.');
                }
            };
        });
    }
}
