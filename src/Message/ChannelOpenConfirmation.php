<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
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

    public function encode(): string
    {
        return parent::encode() . \pack('N3', $this->senderChannel, $this->initialWindowSize, $this->maximumPacketSize);
    }

    public static function decode(): \Generator
    {
        [$recipient, $sender, $initWindowSize, $maxPacketSize] = yield from Ssh\times(4, Ssh\uint32(...));

        return new static($recipient, $sender, $initWindowSize, $maxPacketSize);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_OPEN_CONFIRMATION;
    }
}
