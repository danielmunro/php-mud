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
                    $reduce = reduce_left($input->getArgs(), function (string $value, int $index) {
                        return $index > 0 ? $value : '';
                    });
                    $message = $input->getMob()->getName().' gossips "'.$reduce.'"';
                    /**
                    each(
                        $server->getClients()->toArray(),
                        function (Client $client) use ($input, $message, $reduce) {
                            if ($client === $input->getClient()) {
                                $client->write('You gossip "'.$reduce.'"');
                            } else {
                                $client->write($message);
                            }
                        }
                    );
                     */
                }
            };
        });
    }
}
