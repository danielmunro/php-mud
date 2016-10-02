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

        if ($property === 'debug') {
            return $this->debugInfo($room);
        } elseif (method_exists($room, $setter)) {
            return $this->setRoomProperty(
                $room,
                $setter,
                implode(' ', array_slice($args->toArray(), 2))
            );
        }

        try {
            //$direction = new Direction($property);



            //$directionEntity = new \PhpMud\Entity\Direction()
        } catch (\UnexpectedValueException $e) {
        }

        return new Output('Not implemented yet');
        //return new Output("That doesn't make any sense");
    }

    protected function debugInfo(\PhpMud\Entity\Room $room): Output
    {
        return new Output('Room ID: '.$room->getId());
    }

    protected function setRoomProperty(\PhpMud\Entity\Room $room, string $setter, string $value): Output
    {
        $room->$setter($value);

        return new Output('Room updated');
    }
}
