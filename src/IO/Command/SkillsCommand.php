<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
use PhpMud\Entity\Ability;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use PhpMud\Skill\Skill;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\reduce_left;

class SkillsCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['skills'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    return new Output(
                        sprintf(
                            "Your skills:\n%s",
                            reduce_left(
                                $input->getMob()->getAbilities()->filter(function (Ability $ability) {
                                    return $ability->getAbility() instanceof Skill;
                                })->toArray(),
                                function (Ability $ability, int $index, array $collection, string $reduction) {
                                    return sprintf(
                                        "%s\n%s",
                                        $reduction,
                                        $ability->getName()
                                    );
                                },
                                ''
                            )
                        )
                    );
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
