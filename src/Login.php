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

use PhpMud\Entity\Mob;
use PhpMud\IO\Input;

/**
 *
 */
class Login
{
    const STATE_NAME = 'name';

    const STATE_COMPLETE = 'complete';

    /**
     * @var int
     */
    protected $state;

    /**
     * @var Mob
     */
    protected $mob;

    public function __construct()
    {
        $this->state = static::STATE_NAME;
    }

    public function next(Input $input): string
    {
        $this->mob = new Mob((string) $input);
        $this->state = static::STATE_COMPLETE;

        return $this->state;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function getMob(): Mob
    {
        return $this->mob;
    }
}
