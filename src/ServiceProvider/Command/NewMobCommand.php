<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class NewMobCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['mobfact'] = $pimple->protect(function(Client $client) {
            return new class implements Command
            {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Input $input): Output
                {
                    $mob = new Mob('a fresh mob');

                    $input->getRoom()->getMobs()->add($mob);
                    $mob->setRoom($input->getRoom());

                    return new Output('', 'A fresh mob arrives from the mob factory.');
                }
            };
        });
    }
}
