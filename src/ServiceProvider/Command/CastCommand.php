<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class CastCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['cast'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {


                    return new Output('What would you like to cast?');
                }
            };
        });

        $pimple['c'] = $pimple['cast'];
    }
}