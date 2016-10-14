<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command\Look;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class LookCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['look'] = $pimple->protect(function () {
            return new Look();
        });
    }
}
