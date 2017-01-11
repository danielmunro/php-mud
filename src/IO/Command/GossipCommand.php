<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Client;
use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\each;
use function Functional\with;

class GossipCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['gossip'] = $pimple->protect(function () {
            return new class() implements Command {
                public function execute(Server $server, Input $input): Output
                {
                    return with(
                        $input->getAssigningValue(1),
                        function (string $message) use ($input, $server) {
                            $messageToClients = sprintf(
                                "%s gossips, \"%s\"\n",
                                (string)$input->getMob(),
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
                    ) ?? new Output('Gossip what?');
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
