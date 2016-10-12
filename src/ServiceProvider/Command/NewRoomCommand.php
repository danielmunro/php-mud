<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Client;
use PhpMud\Command\NewRoom;
use PhpMud\Service\DirectionService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\last;

class NewRoomCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['new room'] = $pimple->protect(function (Client $client) {

            return new NewRoom(
                (new DirectionService())->matchPartialString(
                    last(
                        $client->getArgs()
                    )
                )
            );
        });
    }
}
