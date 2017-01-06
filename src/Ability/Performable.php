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

namespace PhpMud\Ability;

use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use PhpMud\IO\Output;

interface Performable
{
    public function rollDice(): int;

    public function applySuccessCost(Mob $mob);

    public function applyFailCost(Mob $mob);

    public function canPerform(Mob $mob): bool;

    public function perform(Input $input): Output;

    public function getDelay(): int;
}
