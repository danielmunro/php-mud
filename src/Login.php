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
use PhpMud\Enum\Race;
use PhpMud\IO\Input;

class Login
{
    const STATE_NAME = 'name';

    const STATE_RACE = 'race';

    const STATE_COMPLETE = 'complete';

    /**
     * @var int
     */
    protected $state;

    /**
     * @var string
     */
    protected $mobName;

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
        switch ($this->state) {
            case static::STATE_NAME:
                $this->mobName = (string) $input;
                $this->state = static::STATE_RACE;
                break;
            case static::STATE_RACE:
                $this->mob = new Mob($this->mobName, new Race((string) $input));
                $this->state = static::STATE_COMPLETE;
                break;
        }

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
