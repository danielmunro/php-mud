<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QuitCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['quit'] = $pimple->protect(function () {
            return new class() implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    $server->getClients()->removeElement($input->getClient());
                    $input->getClient()->getConnection()->close();
                    $input->getRoom()->getMobs()->removeElement($input->getMob());

                    return new Output('Alas all good things must come to an end.');
                }
            };
        });
    }
}
