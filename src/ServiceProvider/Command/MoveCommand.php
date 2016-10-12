<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command\Move;
use PhpMud\Enum\Direction;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class MoveCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['down'] = $pimple->protect(function () {
            return new Move(Direction::DOWN());
        });
        $pimple['d'] = $pimple['down'];

        $pimple['up'] = $pimple->protect(function () {
            return new Move(Direction::UP());
        });
        $pimple['u'] = $pimple['up'];

        $pimple['north'] = $pimple->protect(function () {
            return new Move(Direction::NORTH());
        });
        $pimple['n'] = $pimple['north'];

        $pimple['south'] = $pimple->protect(function () {
            return new Move(Direction::SOUTH());
        });
        $pimple['s'] = $pimple['south'];

        $pimple['east'] = $pimple->protect(function () {
            return new Move(Direction::EAST());
        });
        $pimple['e'] = $pimple['east'];

        $pimple['west'] = $pimple->protect(function () {
            return new Move(Direction::WEST());
        });
        $pimple['w'] = $pimple['west'];
    }
}
