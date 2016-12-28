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

    protected $buffer = [];

    protected $lastInput = '';

    /** @var Mob $mob */
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
        if ($this->buffer && $this->mob && !$this->mob->getDelay()) {
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
        $input = trim($input);
        if ($input === '!') {
            $input = $this->lastInput;
        } else {
            $this->lastInput = $input;
        }
        return new Input($input, $this);
    }

    public function prompt()
    {
        return sprintf('%dhp %dmana %dmv> ', $this->mob->getHp(), $this->mob->getMana(), $this->mob->getMv());
    }

    public function write(string $output)
    {
        $this->connection->write($output);
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
}
