<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;

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
    public static function decode(SshBinary $data): self
    {
        $recipient = $data->readInt();
        $sender = $data->readInt();
        $initWindowSize = $data->readInt();
        $maxPacketSize  = $data->readInt();

        return new static($recipient, $sender, $initWindowSize, $maxPacketSize);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_OPEN_CONFIRMATION;
    }
}
