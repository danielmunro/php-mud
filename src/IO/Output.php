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
use PhpMud\Enum\OutputStatus;

/**
 * Command output
 */
class Output
{
    /**
     * @var string
     */
    protected $response;

    /**
     * @var string
     */
    protected $roomMessage;

    /**
     * @param string $response
     * @param string $roomMessage
     */
    public function __construct(string $response, string $roomMessage = '')
    {
        $this->response = $response;
        $this->roomMessage = $roomMessage;
    }

    /**
     * @return string
     */
    public function getResponse(): string
    {
        return $this->response;
    }

    public function getRoomMessage(): string
    {
        return $this->roomMessage;
    }

    public function writeResponse(Client $client)
    {
        $client->write(sprintf('%s%s', $this->response ? $this->response."\n" : '', $client->prompt()));
    }

    public function __toString()
    {
        return $this->response;
    }
}
