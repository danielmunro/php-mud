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

namespace PhpMud\Entity\Skill;

use PhpMud\Entity\PrimaryKeyTrait;
use PhpMud\Entity\Skill;

/**
 * @Entity
 */
class Bash extends Skill
{
    use PrimaryKeyTrait;

    public function getIdentifiers(): array
    {
        return [
            'bash',
            'b'
        ];
    }
}