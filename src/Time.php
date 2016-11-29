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

namespace PhpMud;

class Time
{
    const TICKS_PER_DAY = 24;
    const DAYS_PER_WEEK = 7;

    const MORNING_HOUR = 5;
    const TWILIGHT_HOUR = 20;

    const VISIBILITY_LOW = 10;
    const VISIBILITY_MEDIUM = 50;
    const VISIBILITY_HIGH = 80;

    protected $hour = 0;
    protected $day = 0;
    protected $week = 0;
    protected $month = 0;
    protected $year = 0;

    public function __construct(int $hour = 0)
    {
        if ($hour) {
            $this->setHour($hour);
        }
    }

    protected function setHour(int $hour)
    {
        $this->hour = $hour;

        if ($this->hour >= self::TICKS_PER_DAY) {
            $this->day += floor($this->hour / self::TICKS_PER_DAY);
            $this->hour = $this->hour % self::TICKS_PER_DAY;
        }
    }

    public function incrementTime()
    {
        $this->setHour($this->hour + 1);
    }

    public function getHour(): int
    {
        return $this->hour;
    }

    public function getVisibility(): int
    {
        if ($this->hour < self::MORNING_HOUR || $this->hour > self::TWILIGHT_HOUR) {
            return self::VISIBILITY_LOW;
        } elseif (
            ($this->hour >= self::MORNING_HOUR && $this->hour <= self::MORNING_HOUR + 1)
            || $this->hour === self::TWILIGHT_HOUR) {
            return self::VISIBILITY_MEDIUM;
        }

        return self::VISIBILITY_HIGH;
    }
}