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

    public function __construct(string $name, $timeout = 0, Attributes $attributes = null)
    {
        $this->name = $name;
        $this->timeout = $timeout;
        $this->attributes = $attributes;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getTimeout(): int
    {
        return $this->timeout;
    }

    public function decrementTimeout(): int
    {
        return $this->timeout--;
    }
}
