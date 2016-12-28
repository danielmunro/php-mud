<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Command;
use PhpMud\Enum\Disposition;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class SitCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['sit'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if ($input->getDisposition()->equals(Disposition::SITTING())) {
                        return new Output('You are already sitting.');
                    }

                    if ($input->getDisposition()->equals(Disposition::FIGHTING())) {
                        return new Output('No way! You are fighting.');
                    }

                    $input->getMob()->setDisposition(Disposition::SITTING());

                    return new Output('You sit.');
                }
            };
        });
    }
}
