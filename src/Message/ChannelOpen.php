<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelType;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        [$type, $sender, $initWindowSize, $maxPacketSize] = Strings::unpackSSH2('sN3', $data);
        return new static(ChannelType::from($type), $sender, $initWindowSize, $maxPacketSize);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_OPEN;
    }
}
