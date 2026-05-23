<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;

final class ChannelWindowAdjust extends Channel
{
    public function __construct(int $recipientChannel, public readonly int $windowIncrement)
    {
        parent::__construct($recipientChannel);
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . \pack('N', $this->windowIncrement);
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $channel    = $data->readInt();
        $bytesToAdd = $data->readInt();

        return new static($channel, $bytesToAdd);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_WINDOW_ADJUST;
    }
}
