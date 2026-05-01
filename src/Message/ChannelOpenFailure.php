<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;

final class ChannelOpenFailure extends Channel
{
    public function __construct(int $recipientChannel, public readonly int $reasonCode, public readonly string $description, public readonly string $languageTag)
    {
        parent::__construct($recipientChannel);
    }

    public function encode(): string
    {
        return parent::encode() . \pack(
            'N2a*Na*',
            $this->reasonCode,
            \strlen($this->description),
            $this->description,
            \strlen($this->languageTag),
            $this->languageTag,
        );
    }

    public static function decode(): \Generator
    {
        [$recipient,   $reasonCode]  = yield from Ssh\times(2, Ssh\uint32(...));
        [$description, $languageTag] = yield from Ssh\times(2, Ssh\string(...));

        return new static($recipient, $reasonCode, $description, $languageTag);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_CHANNEL_OPEN_FAILURE;
    }
}
