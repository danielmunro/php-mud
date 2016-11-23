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
use PhpMud\Entity\Mob;
use PhpMud\Entity\Room;
use function Functional\tail;
use function Functional\select;
use PhpMud\Noun;

class Input
{
    /**
     * @var Client $client
     */
    protected $client;

    /**
     * @var array $args
     */
    protected $args;

    /**
     * @var string $command
     */
    protected $command;

    public function __construct(Client $client, string $input)
    {
        $this->client = $client;
        $this->args = explode(' ', $input);
        $this->command = $this->args[0];
        $this->input = $input;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function getMob(): Mob
    {
        return $this->client->getMob();
    }

    public function getRoom(): Room
    {
        return $this->client->getMob()->getRoom();
    }

    public function isSubjectMatch(Noun $noun): bool
    {
        return count($this->args) > 1 ? !empty(select(
            $noun->getIdentifiers(),
            function(string $identifier) {
                return strpos($identifier, $this->args[1]) === 0;
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

    public function __toString()
    {
        return $this->input;
    }
}
