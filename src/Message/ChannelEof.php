<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;

final class ChannelEof extends Channel
{
    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_EOF;
    }
}
