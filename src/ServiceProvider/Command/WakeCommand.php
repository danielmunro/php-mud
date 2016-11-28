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

class WakeCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['wake'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if ($input->getDisposition()->equals(Disposition::STANDING())) {
                        return new Output('You are already standing.');
                    }

                    $input->getMob()->setDisposition(Disposition::STANDING());

                    return new Output('You stand up.');
                }
            };
        });
    }
}
