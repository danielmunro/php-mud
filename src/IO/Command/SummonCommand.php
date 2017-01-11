<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Race\Human;
use PhpMud\Race\Race;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\with;

class SummonCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['summon'] = $pimple->protect(function () {
            return new class implements Command
            {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Server $server, Input $input): Output
                {
                    if (!$input->getDisposition()->canInteract()) {
                        return $input->getClient()->getDispositionCheckFail();
                    }

                    $race = with(
                        $input->getSubject(),
                        function (string $race) {
                            try {
                                return Race::matchPartialValue($race);
                            } catch (\UnexpectedValueException $e) {
                                return new Human();
                            }
                        }
                    ) ?? new Human();

                    $mob = new Mob(
                        sprintf('a fresh %s', (string)$race),
                        $race
                    );

                    $mob->setRoom($input->getRoom());

                    return new Output(sprintf('%s arrives from the mob factory.', (string)$mob));
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::BUILDER();
                }
            };
        });
    }
}
