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

namespace PhpMud\Job;

use PhpMud\Enum\Job as JobEnum;
use PhpMud\Job\Job as JobInterface;

abstract class JobFactory
{
    public static function matchPartialValue(string $value): JobInterface
    {
        if (strpos(JobEnum::CLERIC, $value) === 0) {
            return new Cleric();
        } elseif (strpos(JobEnum::MAGE, $value) === 0) {
            return new Mage();
        } elseif (strpos(JobEnum::THIEF, $value) === 0) {
            return new Thief();
        } elseif (strpos(JobEnum::WARRIOR, $value) === 0) {
            return new Warrior();
        } elseif (strpos(JobEnum::UNINITIATED, $value) === 0) {
            return new Uninitiated();
        }

        throw new \UnexpectedValueException(sprintf('unknown value: %s', $value));
    }
}
