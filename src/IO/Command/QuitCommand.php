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
use function Functional\with;

class QuitCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['quit'] = $pimple->protect(function () {
            return new class() implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    with(
                        $input->getClient(),
                        function (Client $client) use ($server) {
                            $server->getClients()->removeElement($client);
                            if ($client->getMob()) {
                                Server::removeMob($client->getMob());
                                $client->getMob()->getRoom()->getMobs()->removeElement($client->getMob());
                            }
                            $client->getConnection()->close();
                        }
                    );

                    return new Output('Alas all good things must come to an end.');
                }
            };
        });
    }
}
