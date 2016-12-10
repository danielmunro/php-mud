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

use PhpMud\Entity\Attributes;

abstract class Job
{
    const CLERIC = 'cleric';
    const MAGE = 'mage';
    const THIEF = 'thief';
    const WARRIOR = 'warrior';
    const UNINITIATED = 'uninitiated';

    protected $startingAttributes;

    public function getStartingAttributes(): Attributes
    {
        return $this->startingAttributes;
    }

    public static function matchPartialValue(string $value): Job
    {
        if (strpos(self::CLERIC, $value) === 0) {
            return new Cleric();
        } elseif (strpos(self::MAGE, $value) === 0) {
            return new Mage();
        } elseif (strpos(self::THIEF, $value) === 0) {
            return new Thief();
        } elseif (strpos(self::WARRIOR, $value) === 0) {
            return new Warrior();
        } elseif (strpos(self::UNINITIATED, $value) === 0) {
            return new Uninitiated();
        }

        throw new \UnexpectedValueException(sprintf('unknown value: %s', $value));
    }

    abstract public function __toString(): string;
}
