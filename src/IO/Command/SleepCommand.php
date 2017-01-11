<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
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
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    if ($input->getDisposition()->equals(Disposition::FIGHTING())) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    if ($input->getDisposition()->equals(Disposition::SLEEPING())) {
                        return new Output('You are already sleeping.');
                    }

                    $input->getMob()->setDisposition(Disposition::SLEEPING());

                    return new Output('You go to sleep.');
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
