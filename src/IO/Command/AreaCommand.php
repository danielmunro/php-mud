<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Color;
use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Affect;
use PhpMud\Entity\Area;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;

class AreaCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['area'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    switch ($input->getSubject()) {
                        case 'new':
                            $area = new Area(implode(' ', array_slice($input->getArgs(), 2)));
                            $area->addRoom($input->getRoom());

                            return new Output('You create a new area.');

                        case 'name':
                            $input->getRoom()->getArea()->setName(implode(' ', array_slice($input->getArgs(), 2)));

                            return new Output('You renamed the area.');

                        case 'info':
                        case '':
                            return new Output(
                                sprintf(
                                    'Area name is %s.',
                                    Color::cyan((string)$input->getRoom()->getArea())
                                )
                            );

                        default:
                            return new Output('Area command not understood.');
                    }

                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
