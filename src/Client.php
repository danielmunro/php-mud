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
use PhpMud\Entity\Mob;
use PhpMud\IO\Input;
use React\Socket\Connection;

class Client
{
    const EVENT_DATA = 'data';

    /**
     * @var array
     */
    protected $buffer = [];

    /**
     * @var int
     */
    protected $delay = 0;

    /**
     * @var Mob
     */
    protected $mob;

    /**
     * @var Connection $connection
     */
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    public function pushBuffer(string $buffer)
    {
        $this->buffer[] = $buffer;
    }

    public function readBufferIfNotDelayed()
    {
        if (!$this->delay && $this->buffer) {
            return $this->input(array_shift($this->buffer));
        }

        return null;
    }

    public function getBuffer(): array
    {
        return $this->buffer;
    }

    public function getConnection(): Connection
    {
        return $this->connection;
    }

    public function input(string $input): Input
    {
        return new Input($this, trim($input));
    }

    public function prompt()
    {
        return sprintf('%dhp %dmana %dmv> ', $this->mob->getHp(), $this->mob->getMana(), $this->mob->getMv());
    }

    public function write(string $output)
    {
        $this->connection->write($output);
    }

    public function pulse()
    {
        if ($this->delay > 0) {
            $this->delay--;
        }

        if ($this->mob && $this->mob->getFight()) {
            $this->mob->getFight()->turn();

            if ($this->mob->getFight()) {
                $target = $this->mob->getFight()->getTarget();
                $this->write($target->getName() . ' ' . static::getCondition($target) . ".\n");
            }

            $this->connection->write("\n ".$this->prompt());
        }
    }

    public function getMob()
    {
        return $this->mob;
    }

    public function setMob(Mob $mob)
    {
        $this->mob = $mob;
        $mob->setClient($this);
    }

    public function getDispositionCheckFail(): Output
    {
        return new Output(sprintf('No way! You are %s.', $this->mob->getDisposition()->getValue()));
    }

    public function getCondition(): string
    {
        $hpPercent = $this->mob->getHp() / $this->mob->getAttribute('hp');

        switch ($hpPercent) {
            case $hpPercent >= 1.0:
                return 'is in excellent condition';
            case $hpPercent > 0.9:
                return 'has a few scratches';
            case $hpPercent > 0.75:
                return 'has some small wounds and bruises';
            case $hpPercent > 0.5:
                return 'has quite a few wounds';
            case $hpPercent > 0.3:
                return 'has some big nasty wounds and scratches';
            case $hpPercent > 0.15:
                return 'looks pretty hurt';
            case $hpPercent >= 0.0:
                return 'is in awful condition';
            default:
                return 'is bleeding to death.';
        }
    }
}
