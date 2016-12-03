<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\each;
use function Functional\reduce_left;

class GossipCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['gossip'] = $pimple->protect(function () {
            return new class() implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    if (count($input->getArgs()) === 1) {
                        return new Output('Gossip what?');
                    }

                    $strInput = (string)$input;
                    $message = substr($strInput, strpos($strInput, ' '));
                    $messageToClients = sprintf(
                        "%s gossips, \"%s\"\n",
                        $input->getMob()->getName(),
                        $message
                    );

                    each(
                        $server->getClients()->toArray(),
                        function (Client $client) use ($input, $messageToClients) {
                            if ($client !== $input->getClient()) {
                                $client->write($messageToClients);
                            }
                        }
                    );

                    return new Output(sprintf("You gossip, \"%s\"\n", $message));
                }
            };
        });
    }
}
