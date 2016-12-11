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

namespace PhpMud;

function dInt($int): int
{
    return random_int(1, $int);
}

function d20(): int
{
    return random_int(1, 20);
}
