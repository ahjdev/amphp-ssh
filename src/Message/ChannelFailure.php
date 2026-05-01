<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;

final class ChannelFailure extends Channel
{
    public static function getNumber(): int
    {
        return self::SSH_MSG_CHANNEL_FAILURE;
    }
}
