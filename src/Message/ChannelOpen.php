<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\ChannelType;
use Amp\Ssh\SshMessage;

final class ChannelOpen extends SshMessage
{
    public function __construct(
        private ChannelType $type,
        private int $senderChannel,
        private int $initialWindowSize = 0x7FFFFFFF,
        private int $maximumPacketSize = 0x4000,
    ) {
    }

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

    public static function decode(): \Generator
    {
        $type = yield from Ssh\string();
        [$sender, $initWindowSize, $maxPacketSize] = yield from Ssh\times(3, Ssh\uint32(...));

        return new static(ChannelType::from($type), $sender, $initWindowSize, $maxPacketSize);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_CHANNEL_OPEN;
    }
}
