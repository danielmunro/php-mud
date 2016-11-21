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
 * Class Channel
 *
 * @Entity
 */
class Channel
{
    use PrimaryKeyTrait;

    /** @ManyToOne(targetEntity="Mob", inversedBy="channels") */
    protected $mob;

    /** @Column(type="bool") */
    protected $isListening;
}