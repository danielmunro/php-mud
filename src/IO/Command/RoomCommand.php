<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\Server;
use PhpMud\IO\Command\Command;
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
                        $input->getRoom()->setTitle($input->getAssigningValue());
                    } elseif (stripos('description', $input->getSubject()) === 0) {
                        $input->getRoom()->setDescription($input->getAssigningValue());
                    } else {
                        return new Output('Options are: title, description');
                    }

                    $server->persist();

                    return new Output('Room updated.');
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
