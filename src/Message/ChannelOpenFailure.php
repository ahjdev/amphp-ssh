<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;

final class ChannelOpenFailure extends Channel
{
    public function __construct(int $recipientChannel, public readonly int $reasonCode, public readonly string $description, public readonly string $languageTag)
    {
        parent::__construct($recipientChannel);
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . \pack(
            'N2a*Na*',
            $this->reasonCode,
            \strlen($this->description), $this->description,
            \strlen($this->languageTag), $this->languageTag,
        );
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $channel = $data->readInt();
        $reasonCode = $data->readInt();
        $description = $data->readString();
        $languageTag = $data->readString();

        return new static($channel, $reasonCode, $description, $languageTag);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_OPEN_FAILURE;
    }
}
