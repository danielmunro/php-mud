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

namespace PhpMud\Command;

use PhpMud\Client;
use PhpMud\Command;
use PhpMud\Input;
use PhpMud\Output;

class Quit implements Command
{
    /** @var Client $client */
    protected $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * {@inheritdoc}
     */
    public function execute(Input $input): Output
    {
        $this->client->disconnect();

        return new Output('');
    }
}