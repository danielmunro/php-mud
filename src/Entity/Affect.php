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

namespace PhpMud\Entity;

use PhpMud\Enum\Affect as AffectEnum;

/**
 * @Entity
 */
class Affect
{
    use PrimaryKeyTrait;

    /** @Column(type="string") */
    protected $name;

    /** @OneToOne(targetEntity="Attributes") */
    protected $attributes;

    /** @Column(type="integer") */
    protected $timeout;

    /** @ManyToOne(targetEntity="Mob", inversedBy="affects") */
    protected $mob;

    /** @ManyToOne(targetEntity="Item", inversedBy="affects") */
    protected $item;

    /** @var AffectEnum $enum */
    protected $enum;

    public function __construct(string $name, $timeout = 0, Attributes $attributes = null)
    {
        $this->name = $name;
        $this->timeout = $timeout;
        $this->attributes = $attributes;
        $this->postLoad();
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function getEnum(): AffectEnum
    {
        return $this->enum;
    }

    public function decrementTimeout()
    {
        $this->timeout--;
    }

    public function getAttribute(string $attribute): int
    {
        return $this->attributes->getAttribute($attribute);
    }

    public function postLoad()
    {
        $this->enum = new AffectEnum($this->name);
    }

    public function __toString(): string
    {
        return $this->name;
    }
}
