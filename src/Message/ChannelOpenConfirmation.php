<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class ChannelOpenConfirmation extends Channel
{
    public function __construct(
        int $recipientChannel,
        public readonly int $senderChannel,
        public readonly int $initialWindowSize,
        public readonly int $maximumPacketSize,
    ) {
        parent::__construct($recipientChannel);
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . \pack('N3', $this->senderChannel, $this->initialWindowSize, $this->maximumPacketSize);
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$recipient, $sender, $initWindowSize, $maxPacketSize] = Strings::unpackSSH2('N4', $data);
        return new static($recipient, $sender, $initWindowSize, $maxPacketSize);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_OPEN_CONFIRMATION;
    }
}
