<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QuitCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['quit'] = $pimple->protect(function (Client $client) {
            return new class($client) implements Command {
                /** @var Client  */
                protected $client;

                public function __construct(Client $client)
                {
                    $this->client = $client;
                }

                public function execute(Input $input): Output
                {
                    $this->client->disconnect();

                    return new Output('');
                }
            };
        });
    }
}
