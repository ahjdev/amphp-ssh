<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        [$recipientChannel, $windowIncrement] = Strings::unpackSSH2('N2', $data);
        return new static($recipientChannel, $windowIncrement);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_WINDOW_ADJUST;
    }
}
