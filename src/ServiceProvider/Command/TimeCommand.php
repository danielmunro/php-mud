<?php
declare(strict_types=1);

namespace PhpMud\ServiceProvider\Command;

use PhpMud\Command;
use PhpMud\Enum\Day;
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Server;
use PhpMud\Time;
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
                    $timeOfDay = $server->getTime()->getHour() % Time::TICKS_PER_DAY;

                    return new Output(
                        sprintf(
                            'It is %s %s, day of %s.',
                            $timeOfDay === 0 ? 12 : $timeOfDay,
                            $timeOfDay >= 12 ? 'pm' : 'am',
                            Day::fromIndex(
                                ($server->getTime()->getHour() / Time::TICKS_PER_DAY) % Time::DAYS_PER_WEEK
                            )
                        )
                    );
                }
            };
        });
    }
}
