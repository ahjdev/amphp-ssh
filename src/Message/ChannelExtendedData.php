<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;

final class ChannelExtendedData extends Channel
{
    const SSH_EXTENDED_DATA_STDERR = 1;

    public function __construct(int $recipientChannel, public readonly int $type, public readonly string $data)
    {
        parent::__construct($recipientChannel);
    }

    public function encode(): string
    {
        return parent::encode() . \pack('N2a*', $this->type, \strlen($this->data), $this->data);
    }

    public static function decode(SshBinary $data): self
    {
        $recipientChannel = $data->readInt();
        $type = $data->readInt();
        $data = $data->readString();

        return new static($recipientChannel, $type, $data);
    }

    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_EXTENDED_DATA;
    }
}
