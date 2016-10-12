<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command\Move;
use PhpMud\Enum\Direction;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class WestCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $svc = $pimple->protect(function () {
            return new Move(Direction::WEST());
        });

        $pimple['west'] = $svc;
        $pimple['w'] = $svc;
    }
}
