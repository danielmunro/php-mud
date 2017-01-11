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
use PhpMud\Entity\Area;
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use function Functional\first;
use function Functional\select;
use PhpMud\Enum\Disposition;
use PhpMud\Noun;

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

    /**
     * @var int $skipCount
     */
    protected $skipCount = 0;

    public function __construct(string $input, Client $client = null, Mob $mob = null)
    {
        $input = trim($input);
        $this->client = $client;
        if ($mob) {
            $this->mob = $mob;
        } elseif ($this->client) {
            $this->mob = $this->client->getMob();
        }
        $this->args = explode(' ', $input);
        $this->command = $this->args[0];
        $this->input = $input;
        $this->subject = $this->args[1] ?? null;
        if ($this->subject && strpos($this->subject, '.') !== false) {
            list($this->skipCount, $this->subject) = explode('.', $this->subject);
            $this->skipCount = (int)$this->skipCount;
        }
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

    public function getArea(): Area
    {
        return $this->mob->getRoom()->getArea();
    }

    public function getRoomMob(callable $filter = null): ?Mob
    {
        return first(
            $this->mob->getRoom()->getMobs()->toArray(),
            $filter ?: function (Mob $mob) {
                return $this->isSubjectMatch($mob);
            }
        );
    }

    public function isAbilityMatch(Ability $ability): bool
    {
         return strpos($ability->getName(), $this->command) === 0;
    }

    public function isSubjectMatch(Noun $noun): bool
    {
        return $this->subject ? !empty(select(
            $noun->getIdentifiers(),
            function (string $identifier) {
                if (stripos($identifier, $this->subject) === 0) {
                    if ($this->skipCount === 0) {
                        return true;
                    } else {
                        $this->skipCount--;
                    }
                }
            }
        )) : false;
    }

    public function isOptionMatch(Noun $noun): bool
    {
        return $this->getOption() ? !empty(select(
            $noun->getIdentifiers(),
            function (string $identifier) {
                if (stripos($identifier, $this->getOption()) === 0) {
                    if ($this->skipCount === 0) {
                        return true;
                    } else {
                        $this->skipCount--;
                    }
                }
            }
        )) : false;
    }

    public function getAssigningValue(int $start = 2): string
    {
        return implode(' ', array_slice($this->args, $start));
    }

    public function getArgs(): array
    {
        return $this->args;
    }

    public function getCommand(): string
    {
        return $this->command;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function getOption(): ?string
    {
        return $this->args[2] ?? null;
    }

    public function __toString()
    {
        return $this->input;
    }
}
