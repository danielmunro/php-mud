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

namespace PhpMud\Command;

use PhpMud\Command;
use PhpMud\Enum\Direction;
use PhpMud\Input;
use PhpMud\Output;

class South implements Command
{
    use Move;

    public function execute(Input $input): Output
    {
        return $this->move($input->getMob(), Direction::SOUTH());
    }
}