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

/**
 * Command output
 */
class Output
{
    /**
     * @var string
     */
    protected $output;

    /**
     * @param string $output
     */
    public function __construct(string $output)
    {
        $this->output = $output;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }
}
