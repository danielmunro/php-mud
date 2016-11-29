<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WeatherCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['weather'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getRoom()->isOutside()) {
                        return new Output("You are indoors and can't see the weather.");
                    }
                    
                    return new Output(
                        sprintf(
                            'Weather is static, visibility is %s.',
                            $server->getTime()->getVisibility() + $input->getRoom()->getVisibility() <=
                                $input->getMob()->getRace()->getVisibilityDeficit() ? 'low' : 'adequate'
                        )
                    );
                }
            };
        });
    }
}
