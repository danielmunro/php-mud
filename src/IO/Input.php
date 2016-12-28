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

use PhpMud\Client;
use PhpMud\Entity\Ability;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use function Functional\tail;
use function Functional\select;
use PhpMud\Enum\Disposition;
use PhpMud\Noun;
use PhpMud\Performable;

class Input
{
    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @var Mob $mob
     */
    protected $mob;

    /**
     * @var array $args
     */
    protected $args;

    /**
     * @var string $command
     */
    protected $command;

    /**
     * @var string $subject
     */
    protected $subject;

    public function __construct(string $input, Client $client = null)
    {
        $input = trim($input);
        $this->client = $client;
        if ($this->client) {
            $this->mob = $this->client->getMob();
        }
        $this->args = explode(' ', $input);
        $this->command = $this->args[0];
        $this->subject = $this->args[1] ?? '';
        $this->input = $input;
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setMob(Mob $mob)
    {
        $this->mob = $mob;
    }

    public function getMob(): Mob
    {
        return $this->mob;
    }

    public function getTarget()
    {
        if ($this->mob->getFight()) {
            return $this->mob->getFight()->getTarget();
        }
    }

    public function getDisposition(): Disposition
    {
        return $this->mob->getDisposition();
    }

    public function getRoom(): Room
    {
        return $this->mob->getRoom();
    }

    public function isAbilityMatch(Ability $ability): bool
    {
         return strpos($ability->getName(), $this->command) === 0;
    }

    public function isSubjectMatch(Noun $noun): bool
    {
        return count($this->args) > 1 ? !empty(select(
            $noun->getIdentifiers(),
            function (string $identifier) {
                return stripos($identifier, $this->args[1]) === 0;
            }
        )) : false;
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getSubject(): string
    {
        return $this->subject;
    }

    public function __toString()
    {
        return $this->input;
    }
}
