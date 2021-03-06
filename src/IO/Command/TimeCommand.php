<?php
declare(strict_types=1);

namespace PhpMud\IO\Command;

use PhpMud\Enum\AccessLevel;
use PhpMud\IO\Command\Command;
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
                    $hour = $timeOfDay % 12;

                    return new Output(
                        sprintf(
                            'It is %s %s, day of %s.',
                            $hour === 0 ? 12 : $hour,
                            $timeOfDay >= 12 ? 'pm' : 'am',
                            Day::fromIndex(
                                ($server->getTime()->getHour() / Time::TICKS_PER_DAY) % Time::DAYS_PER_WEEK
                            )
                        )
                    );
                }

                public function getRequiredAccessLevel(): AccessLevel
                {
                    return AccessLevel::MOB();
                }
            };
        });
    }
}
