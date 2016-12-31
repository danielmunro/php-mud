<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Command;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class QuietCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['quiet'] = $pimple->protect(function () {
            return new class implements Command
            {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    $input->getClient()->setQuiet(!$input->getClient()->isQuiet());

                    return new Output(
                        sprintf(
                            'You %s quiet mode.',
                            $input->getClient()->isQuiet() ? 'enable' : 'disable'
                        )
                    );
                }
            };
        });
    }
}
