<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;

final class ChannelFailure extends Channel
{
    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_FAILURE;
    }
}
