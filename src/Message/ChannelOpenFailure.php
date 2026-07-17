<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        [$channel, $reasonCode, $description, $languageTag] = Strings::unpackSSH2('N2s2', $data);
        return new static($channel, $reasonCode, $description, $languageTag);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::CHANNEL_OPEN_FAILURE;
    }
}
