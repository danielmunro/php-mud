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

namespace PhpMud\IO;

use PhpMud\Enum\CommandResult;

/**
 * Command output
 */
class Output
{
    /**
     * @var
     */
    protected $commandResult;

    /**
     * @var string
     */
    protected $output;

    /**
     * @param string $output
     * @param CommandResult|null $commandResult
     */
    public function __construct(string $output, CommandResult $commandResult = null)
    {
        $this->output = $output;
        $this->commandResult = $commandResult ?: CommandResult::SUCCESS();
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @return CommandResult
     */
    public function getCommandResult(): CommandResult
    {
        return $this->commandResult;
    }
}