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
class Equipped extends Item
{
    use PrimaryKeyTrait;

    /** @OneToOne(targetEntity="Equipment") */
    protected $light;

    /** @OneToOne(targetEntity="Equipment") */
    protected $finger1;

    /** @OneToOne(targetEntity="Equipment") */
    protected $finger2;

    /** @OneToOne(targetEntity="Equipment") */
    protected $neck1;

    /** @OneToOne(targetEntity="Equipment") */
    protected $neck2;

    /** @OneToOne(targetEntity="Equipment") */
    protected $torso;

    /** @OneToOne(targetEntity="Equipment") */
    protected $head;

    /** @OneToOne(targetEntity="Equipment") */
    protected $legs;

    /** @OneToOne(targetEntity="Equipment") */
    protected $feet;

    /** @OneToOne(targetEntity="Equipment") */
    protected $neck;
}
