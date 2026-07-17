<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        [$recipientChannel, $data] = Strings::unpackSSH2('Ns', $data);
        return new static($recipientChannel, $data);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_DATA;
    }
}
