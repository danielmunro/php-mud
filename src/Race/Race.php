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

namespace PhpMud\Race;

use Doctrine\Common\Collections\ArrayCollection;
use MyCLabs\Enum\Enum;
use PhpMud\Entity\Attributes;
use PhpMud\Enum\Size;
use PhpMud\Job\Job;

abstract class Race
{
    const HUMAN = 'human';
    const ELF = 'elf';
    const DWARF = 'dwarf';
    const OGRE = 'ogre';
    const FAERIE = 'faerie';
    const GIANT = 'giant';
    const KENDER = 'kender';

    /** @var Attributes $startingAttributes */
    protected $startingAttributes;

    /** @var Enum $visibilityRequirement */
    protected $visibilityRequirement = 0;

    /** @var Size $size */
    protected $size;

    /** @var int $creationPoints */
    protected $creationPoints;

    /** @var array $bonusSkills */
    protected $bonusSkills = [];

    /** @var array $vulns */
    protected $vulns = [];

    /** @var array $resists */
    protected $resists = [];

    public function getStartingAttributes(): Attributes
    {
        return $this->startingAttributes;
    }

    public function getVisibilityRequirement(): Enum
    {
        return $this->visibilityRequirement;
    }

    public function getSize(): Size
    {
        return $this->size;
    }

    public function getCreationPoints(): int
    {
        return $this->creationPoints;
    }

    public function getBonusSkills(): array
    {
        return $this->bonusSkills;
    }

    public function getVulns(): array
    {
        return $this->vulns;
    }

    public function getResists(): array
    {
        return $this->resists;
    }

    public static function matchPartialValue(string $value): Race
    {
        if (strpos(self::HUMAN, $value) === 0) {
            return new Human();
        } elseif (strpos(self::DWARF, $value) === 0) {
            return new Dwarf();
        } elseif (strpos(self::ELF, $value) === 0) {
            return new Elf();
        } elseif (strpos(self::OGRE, $value) === 0) {
            return new Ogre();
        } elseif (strpos(self::FAERIE, $value) === 0) {
            return new Faerie();
        } elseif (strpos(self::GIANT, $value) === 0) {
            return new Giant();
        } elseif (strpos(self::KENDER, $value) === 0) {
            return new Kender();
        }

        throw new \UnexpectedValueException(sprintf('unknown value: %s', $value));
    }

    public static function fromValue(string $value): Race
    {
        switch ($value) {
            case self::HUMAN:
                return new Human();
            case self::DWARF:
                return new Dwarf();
            case self::ELF:
                return new Elf();
            case self::OGRE:
                return new Ogre();
            case self::FAERIE:
                return new Faerie();
            case self::GIANT:
                return new Giant();
            case self::KENDER:
                return new Kender();
            default:
                throw new \UnexpectedValueException($value);
        }
    }

    abstract public function getJobExpMultiplier(Job $job): int;

    abstract public function __toString(): string;
}
