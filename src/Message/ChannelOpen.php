<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\ChannelType;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class ChannelOpen extends SshMessage
{
    public function __construct(
        private ChannelType $type,
        private int $senderChannel,
        private int $initialWindowSize = 0x7FFFFFFF,
        private int $maximumPacketSize = 0x4000,
    ) {
    }

    #[\Override]
    public function encode(): string
    {
        $type = $this->type->value;

        return \pack(
            'CNa*N3',
            self::getNumber(),
            \strlen($type), $type,
            $this->senderChannel, $this->initialWindowSize, $this->maximumPacketSize
        );
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $type = $data->readString();
        $sender = $data->readInt();
        $initWindowSize = $data->readInt();
        $maxPacketSize = $data->readInt();

        return new static(ChannelType::from($type), $sender, $initWindowSize, $maxPacketSize);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_OPEN;
    }
}
