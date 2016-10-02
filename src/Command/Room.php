<?php
declare(strict_types=1);

/**
 * This file is part of the PhpMud package.
 *
 * (c) Dan Munro <dan@danmunro.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace PhpMud\Command;

use PhpMud\Command;
use PhpMud\Enum\Direction;
use PhpMud\IO\Input;
use PhpMud\IO\Output;

/**
 * Modify an existing room
 */
class Room implements Command
{
    /**
     * {@inheritdoc}
     */
    public function execute(Input $input): Output
    {
        $args = $input->getArgs();
        $room = $input->getMob()->getRoom();
        $property = $args[1];
        $setter = 'set'.ucfirst($property);

        if (method_exists($room, $setter)) {
            $value = implode(' ', array_slice($args->toArray(), 2));
            $room->$setter($value);

            return new Output("You change the room's ".$property." to ".$value);
        }

        try {
            //$direction = new Direction($property);



            //$directionEntity = new \PhpMud\Entity\Direction()
        } catch (\UnexpectedValueException $e) {
        }

        return new Output('Not implemented yet');
        //return new Output("That doesn't make any sense");
    }
}