<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use UnexpectedValueException;
use PhpMud\Client;
use PhpMud\Command;
use PhpMud\Entity\Direction;
use PhpMud\Entity\Room;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Service\DirectionService;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\last;

class NewRoomCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['roomfact'] = $pimple->protect(function () {

            return new class implements Command {
                /**
                 * {@inheritdoc}
                 */
                public function execute(Input $input): Output
                {
                    $mob = $input->getMob();
                    $srcRoom = $mob->getRoom();
                    $newRoom = new Room();
                    $newRoom->setTitle('A swirling mist');
                    $newRoom->setDescription('You are engulfed by a mist.');

                    try {
                        $direction = DirectionService::matchPartialString(last($input->getArgs()));
                    } catch (UnexpectedValueException $e) {
                        return new Output('That direction does not exist.');
                    }

                    $srcDirection = new Direction($srcRoom, $direction, $newRoom);
                    $srcRoom->getDirections()->add($srcDirection);

                    $newDirection = new Direction($newRoom, $direction->reverse(), $srcRoom);
                    $newRoom->getDirections()->add($newDirection);

                    return new Output('A room appears '.$direction->getValue());
                }
            };
        });
    }
}
