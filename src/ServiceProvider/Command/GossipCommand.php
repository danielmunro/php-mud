<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\tail;

class GossipCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['gossip'] = $pimple->protect(function (Client $client) {
            return new class($client) implements Command {

                /** @var Client $client */
                protected $client;

                public function __construct(Client $client)
                {
                    $this->client = $client;
                }

                public function execute(Input $input): Output
                {
                    $this->client->gossip(implode(' ', tail($input->getArgs())));
                    return new Output('');
                    //$this->client->emit
                    /**
                    $mob = $input->getMob();
                    each($mob->getRoom()->getMobs(), function(Mob $m) use ($mob) {
                        if ($mob === $m) {

                        }
                    });

                    return new Output('');
                     */
                }
            };
        });
    }
}
