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

namespace PhpMud\SpellGroup;

use PhpMud\Spell\CureLight;

class Healing implements SpellGroup
{
    public function getSpells(): array
    {
        return [
            new CureLight()
        ];
    }
}
