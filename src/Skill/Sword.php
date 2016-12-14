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

use PhpMud\Enum\Disposition;
use PhpMud\Enum\TargetType;
use PhpMud\Job\Job;

class Sword implements Ability, CreationGroup
{
    public function getCreationPoints(Job $job): int
    {
        switch ((string)$job) {
            case Job::WARRIOR:
                return 2;
            case Job::THIEF:
                return 3;
            case Job::CLERIC:
                return 5;
            case Job::MAGE:
            default:
                return 6;
        }
    }

    public function getLevel(Job $job): int
    {
        return 1;
    }

    public function getMinimumDisposition(): Disposition
    {
        return Disposition::FIGHTING();
    }

    public function getTargetType(): TargetType
    {
        return TargetType::NONE();
    }

    public static function getName(): string
    {
        return 'sword';
    }
}
