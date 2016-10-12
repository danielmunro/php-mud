<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command\Move;
use PhpMud\Enum\Direction;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use function Functional\each;

class MoveCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        each(Direction::values(), function (Direction $direction) use ($pimple) {
            $pimple[$direction->getValue()] = $pimple->protect(function () use ($direction) {
                return new Move($direction);
            });
        });
    }
}
