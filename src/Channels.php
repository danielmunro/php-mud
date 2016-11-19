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
use Doctrine\Common\Collections\ArrayCollection;
use PhpMud\Enum\Channel;
use function Functional\each;

/**
 * Channels
 */
class Channels
{
    /** @var array */
    protected $subscribers;

    public function addSubscriber(Channel $channel, ChannelSubscriber $channelSubscriber)
    {
        $channelValue = $channel->getValue();
        if (!isset($this->subscribers[$channelValue])) {
            $this->subscribers[$channelValue] = new ArrayCollection();
        }

        $this->subscribers[$channelValue]->add($channelSubscriber);
    }

    public function removeSubscriber(Channel $channel, ChannelSubscriber $channelSubscriber)
    {
        $this->subscribers[$channel->getValue()]->removeElement($channelSubscriber);
    }

    public function notify(Channel $channel, ChannelSubscriber $originator, string $message)
    {
        each(
            $this->subscribers[$channel->getValue()]->toArray(),
            function (ChannelSubscriber $channelSubscriber) use ($originator, $message) {
                if ($channelSubscriber !== $originator) {
                    $channelSubscriber->notify($message);
                }
            }
        );
    }
}