<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Client;
use PhpMud\Command\Quit;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QuitCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['quit'] = $pimple->protect(function (Client $client) {
            return new Quit($client);
        });
    }
}
