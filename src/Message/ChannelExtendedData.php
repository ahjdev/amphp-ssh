<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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

    public static function decode(string $data): self
    {
        [$recipientChannel, $type, $data] = Strings::unpackSSH2('N2s', $data);

        return new static($recipientChannel, $type, $data);
    }

    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_EXTENDED_DATA;
    }
}
