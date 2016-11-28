<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Entity\Skill;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

use function Functional\first;

class BashCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['bash'] = $pimple->protect(function () {
            return new class implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    $skill = first(
                        $input->getMob()->getSkills()->toArray(),
                        function (Skill $skill) use ($input) {
                            return $input->isSubjectMatch($skill);
                        }
                    );

                    return new Output('You bash around.');
                }
            };
        });

        $pimple['b'] = $pimple['bash'];
    }
}