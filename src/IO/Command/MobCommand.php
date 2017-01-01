<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Color;
use PhpMud\Command;
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
                            first(
                                $input->getRoom()->getMobs()->toArray(),
                                function (Mob $mob) use ($input) {
                                    return $input->isSubjectMatch($mob);
                                }
                            ),
                            function (Mob $mob) use ($input) {
                                switch ($input->getOption()) {
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
                                            $mob->getInventory()->getItems(),
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
                                    default:
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
            };
        });
    }
}
