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

use PhpMud\IO\Output;
use PhpMud\IO\Input;

/**
 * A command
 */
interface Command
{
    /**
     * @param Input $input
     * @return Output
     */
    public function execute(Input $input): Output;
}