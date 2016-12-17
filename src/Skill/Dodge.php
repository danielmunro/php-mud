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
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Job\Job;

class Dodge implements Ability, CreationGroup
{
    public function getCreationPoints(Job $job): int
    {
        switch ((string)$job) {
            case Job::CLERIC:
            case JOB::MAGE:
                return 6;
            case Job::WARRIOR:
            case Job::THIEF:
            default:
                return 15;
        }
    }

    public function getLevel(Job $job): int
    {
        switch ((string)$job) {
            case Job::CLERIC:
                return 20;
            case Job::THIEF:
                return 13;
            case Job::WARRIOR:
                return 1;
            case JOB::MAGE:
            default:
                return 22;
        }
    }

    public function perform(Input $input): Output
    {
        return new Output('');
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
        return 'meditation';
    }
}
