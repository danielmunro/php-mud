<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Color;
use PhpMud\Enum\AccessLevel;
use PhpMud\Entity\Area;
use PhpMud\Enum\Visibility;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\with;

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
                            $area = new Area($input->getAssigningValue());
                            $area->addRoom($input->getRoom());

                            return new Output('You create a new area.');
                        case 'vis':
                            try {
                                $input->getArea()->setVisibility(
                                    new Visibility((int)$input->getAssigningValue())
                                );

                                return new Output('Area visibility set.');
                            } catch (\UnexpectedValueException $e) {
                                return new Output(
                                    sprintf(
                                        'Visibility options are: %s',
                                        implode(' ', Visibility::values())
                                    )
                                );
                            }
                        case 'name':
                            $input->getArea()->setName($input->getAssigningValue());

                            return new Output('You renamed the area.');

                        case 'info':
                        case '':
                            return with(
                                $input->getArea(),
                                function (Area $area) {
                                    return new Output(
                                        sprintf(
                                            'You are in %s. Visibility is %s.',
                                            Color::cyan((string)$area),
                                            Color::cyan((string)$area->getVisibility())
                                        )
                                    );
                                });
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
