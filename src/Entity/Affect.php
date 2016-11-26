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

    public function __construct(string $name, Attributes $attributes, $timeout = 0)
    {
        $this->name = $name;
        $this->attributes = $attributes;
        $this->timeout = $timeout;
    }

    public function decrementTimeout(): int
    {
        return $this->timeout--;
    }
}
