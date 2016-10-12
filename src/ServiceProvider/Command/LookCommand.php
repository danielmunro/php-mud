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
        $svc = $pimple->protect(function () {
            return new Look();
        });

        $pimple['look'] = $svc;
    }
}
