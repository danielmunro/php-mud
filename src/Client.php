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
     * @var Connection
     */
    protected $connection;

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
     * @var Login
     */
    protected $login;

    /**
     * Client constructor.
     *
     * @param Login $login
     * @param Connection $connection
     */
    public function __construct(Login $login, Connection $connection)
    {
        $this->login = $login;
        $this->connection = $connection;
        $connection->on(static::EVENT_DATA, [$this, 'login']);
    }

    public function pushBuffer(string $buffer)
    {
        $this->buffer[] = $buffer;
    }

    public function readBuffer(): Input
    {
        return $this->input(array_shift($this->buffer));
    }

    public function getBuffer(): array
    {
        return $this->buffer;
    }

    public function input(string $input): Input
    {
        return new Input($this, trim($input));
    }

    public function login(string $input)
    {
        if ($this->login->next(new Input($this, $input)) === Login::STATE_COMPLETE) {
            $this->mob = $this->login->getMob();
            $this->mob->setClient($this);
            $this->login = null;
            $this->connection->emit(Server::EVENT_LOGIN, ['mob' => $this->mob]);
            $this->connection->removeListener(static::EVENT_DATA, [$this, 'login']);
            $this->connection->on(static::EVENT_DATA, [$this, 'pushBuffer']);
        }
    }

    public function prompt()
    {
        return sprintf('%dhp %dmana %dmv> ', $this->mob->getHp(), $this->mob->getMana(), $this->mob->getMv());
    }

    /**
     * @param string $output
     */
    public function write(string $output)
    {
        $this->connection->write($output);
    }

    public function close()
    {
        $this->connection->close();
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

    /**
     * @return Mob
     */
    public function getMob(): Mob
    {
        return $this->mob;
    }

    /**
     * @return bool
     */
    public function canReadBuffer(): bool
    {
        return !$this->delay && $this->buffer;
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
