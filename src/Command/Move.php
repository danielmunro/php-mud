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
use PhpMud\IO\Input;
use PhpMud\IO\Output;
use PhpMud\Service\DirectionService;

abstract class Move implements Command
{
    /** @var DirectionService $directionService */
    protected $directionService;

    /**
     * @param DirectionService $directionService
     */
    public function __construct(DirectionService $directionService)
    {
        $this->directionService = $directionService;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Input $input): Output
    {
        return $this->directionService->move(
            $input->getMob(),
            $this->directionService->matchPartialString($input->getArgs()[0])
        );
    }
}
