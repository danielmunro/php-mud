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

use PhpMud\Noun;

/**
 * @Entity
 */
abstract class Skill implements Noun
{
    /** @Column(type="integer") */
    protected $level;

    public function __construct()
    {
        $this->level = 0;
    }
}