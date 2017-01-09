<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Color;
use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Item;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Race\Race;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\with;
use function Functional\first;
use function Functional\last;
use function Functional\each;

class MobCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['mob'] = $pimple->protect(function () {
            return new class implements Command
            {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    return with(
                        $input->getRoomMob(function (Mob $mob) use ($input) {
                            return $input->isSubjectMatch($mob);
                        }),
                        function (Mob $mob) use ($input) {
                            switch ($input->getOption()) {
                                case 'clone':
                                    $newMob = clone $mob;
                                    $input->getRoom()->getMobs()->add($newMob);
                                    return new Output(sprintf('A new %s pops into existence.', (string)$newMob));
                                case 'level':
                                    $level = last($input->getArgs());
                                    if (!is_numeric($level)) {
                                        return new Output('Level must be numeric.');
                                    }

                                    while ($mob->getLevel() < $level) {
                                        $mob->levelUp();
                                    }

                                    return new Output('Ok.');
                                case 'look':
                                    $mob->setLook($input->getAssigningValue(3));

                                    return new Output('Ok.');
                                case 'name':
                                    $oldName = $mob->getName();
                                    $mob->setName($input->getAssigningValue(3));
                                    return new Output(
                                        sprintf(
                                            "You change %s's name to %s.",
                                            Color::cyan($oldName),
                                            Color::cyan((string)$mob)
                                        )
                                    );
                                case 'race':
                                    try {
                                        $mob->setRace(Race::matchPartialValue(last($input->getArgs())));
                                    } catch (\UnexpectedValueException $e) {
                                        return new Output('That race does not exist.');
                                    }
                                    return new Output(
                                        sprintf(
                                            '%s morphs into a %s.',
                                            (string)$mob,
                                            (string)$mob->getRace()
                                        )
                                    );
                                case 'shop':
                                    each(
                                        $mob->getItems(),
                                        function (Item $item) use ($mob) {
                                            $item->setCraftedBy($mob);
                                        }
                                    );
                                    return new Output(
                                        sprintf(
                                            "%s's inventory is set.",
                                            (string)$mob
                                        )
                                    );
                                case '':
                                case 'info':
                                    return new Output('TBD');
                                default:
                                    if (property_exists($mob->getAttributes(), $input->getOption())) {
                                        $mob->getAttributes()->modifyAttribute(
                                            $input->getOption(),
                                            last($input->getArgs())
                                        );
                                        return new Output('Attribute set.');
                                    }
                                    return new Output(
                                        sprintf(
                                            'Unrecognized option: %s',
                                            $input->getSubject()
                                        )
                                    );
                            }
                        }
                    ) ?? new Output("You can't find them.");
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
