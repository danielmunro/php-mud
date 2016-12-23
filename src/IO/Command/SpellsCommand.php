<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Command;
use PhpMud\Entity\Ability;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use PhpMud\Spell\Spell;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;

class SpellsCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['spells'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output(
                        sprintf(
                            "Your spells:\n%s",
                            reduce_left(
                                $input->getMob()->getAbilities()->filter(function (Ability $ability) {
                                    return $ability instanceof Spell;
                                })->toArray(),
                                function (Ability $ability, int $index, array $collection, string $reduction) {
                                    return sprintf(
                                        "%s\n%s",
                                        $reduction,
                                        $ability->getName()
                                    );
                                }
                            )
                        )
                    );
                }
            };
        });
    }
}
