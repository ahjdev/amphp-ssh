<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;

final class ChannelData extends Channel
{
    public function __construct(int $recipientChannel, public readonly string $data)
    {
        parent::__construct($recipientChannel);
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . \pack('Na*', \strlen($this->data), $this->data);
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $recipientChannel = $data->readInt();
        $data = $data->readString();

        return new static($recipientChannel, $data);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_DATA;
    }
}
