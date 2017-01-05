<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\Enum\Affect;
use PhpMud\Entity\Item;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\with;
use function Functional\first;
use function Functional\last;
use function Functional\filter;
use function Functional\reduce_left;

class ItemCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['item'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return with(
                        first(
                            $input->getMob()->getInventory()->getItems(),
                            function (Item $item) use ($input) {
                                return $input->isSubjectMatch($item);
                            }
                        ),
                        function (Item $item) use ($input) {
                            switch ($input->getOption()) {
                                case 'value':
                                    $item->setValue((float)last($input->getArgs()));
                                    return new Output(
                                        sprintf(
                                            "%s's value becomes %d.",
                                            (string)$item,
                                            $item->getValue()
                                        )
                                    );
                                case 'affect':
                                    $affectEnum = new Affect($input->getAssigningValue(3));
                                    $currentAffect = filter(
                                        $item->getAffects(),
                                        function (\PhpMud\Entity\Affect $affect) use ($affectEnum) {
                                            return $affect->getEnum()->equals($affectEnum);
                                        }
                                    );
                                    if ($currentAffect) {
                                        $item->getAffects()->removeElement($currentAffect);
                                        return new Output(
                                            sprintf(
                                                '%s loses affect: %s.',
                                                (string)$item,
                                                $affectEnum->getValue()
                                            )
                                        );
                                    } else {
                                        $item->getAffects()->add(new \PhpMud\Entity\Affect($affectEnum->getValue()));
                                        return new Output(
                                            sprintf(
                                                '%s gains affect: %s.',
                                                (string)$item,
                                                $affectEnum->getValue()
                                            )
                                        );
                                    }
                                case null:
                                default:
                                    return new Output(
                                        sprintf(
                                            "%s:\nvalue: %d\naffects: %s",
                                            (string)$item,
                                            $item->getValue(),
                                            reduce_left(
                                                $item->getAffects()->toArray(),
                                                function (\PhpMud\Entity\Affect $affect, int $index, array $collection, string $reduction) {
                                                    return ($reduction ? $reduction . ', ' : '') . (string)$affect;
                                                },
                                                ''
                                            )
                                        )
                                    );
                            }
                        }
                    );
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
