<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Enum\Disposition;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SleepCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['sleep'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getMob()->getDisposition()->equals(Disposition::FIGHTING())) {
                        return new Output('No way! You are still fighting.');
                    }

                    if (!$input->getMob()->getDisposition()->equals(Disposition::SLEEPING())) {
                        return new Output('You are already sleeping.');
                    }

                    $input->getMob()->setDisposition(Disposition::SLEEPING());

                    return new Output('You lay down and go to sleep.');
                }
            };
        });
    }
}
