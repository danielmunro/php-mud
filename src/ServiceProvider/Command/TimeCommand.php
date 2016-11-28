<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Enum\Time;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class TimeCommand implements ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['time'] = $pimple->protect(function () {
            return new class() implements Command
            {
                public function execute(Server $server, Input $input): Output
                {
                    /**
                    $days = $server->getTime() / Time::TICKS_PER_DAY;
                    $weeks = $days / Time::DAYS_PER_WEEK;
                    $months = $weeks / Time::WEEKS_PER_MONTH;
                    $years = $months / Time::MONTHS_PER_YEAR;
                     */
                    $timeOfDay = $server->getTime() % Time::TICKS_PER_DAY;

                    return new Output(sprintf('It is %d:00', $timeOfDay));
                }
            };
        });
    }
}
