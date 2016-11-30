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

use PhpMud\Entity\Attributes;
use PhpMud\Enum\Size;

abstract class Race
{
    const HUMAN = 'human';
    const ELF = 'elf';
    const DWARF = 'dwarf';
    const OGRE = 'ogre';

    /** @var Attributes $startingAttributes */
    protected $startingAttributes;

    /** @var int $visibilityRequirement */
    protected $visibilityRequirement = 0;

    /** @var Size $size */
    protected $size;

    public function getStartingAttributes(): Attributes
    {
        return $this->startingAttributes;
    }

    public function getVisibilityRequirement(): int
    {
        return $this->visibilityRequirement;
    }

    public function getSize(): Size
    {
        return $this->size;
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
            default:
                throw new \UnexpectedValueException($value);
        }
    }

    abstract public function __toString(): string;
}