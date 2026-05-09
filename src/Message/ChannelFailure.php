<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;

final class ChannelFailure extends Channel
{
    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_FAILURE;
    }
}
