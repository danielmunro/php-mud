<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Server;
use PhpMud\Command;
use PhpMud\Entity\Direction;
use PhpMud\Entity\Room;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\last;
use function Functional\first;

class RoomCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['room'] = $pimple->protect(function () {

            return new class implements Command {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    if (stripos('title', $input->getSubject()) === 0) {
                        $input->getRoom()->setTitle(implode(' ', array_slice($input->getArgs(), 2)));
                    } elseif (stripos('description', $input->getSubject()) === 0) {
                        $input->getRoom()->setDescription(implode(' ', array_slice($input->getArgs(), 2)));
                    } else {
                        return new Output('Options are: title, description');
                    }

                    $server->persist();

                    return new Output('Room updated.');
                }
            };
        });
    }
}
