<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\DisconnectReason;
use Amp\Ssh;
use Amp\Ssh\SshMessage;

final class Disconnect extends SshMessage
{
    public function __construct(
        public readonly DisconnectReason $reasonCode = DisconnectReason::BY_APPLICATION,
        public readonly string $description = '',
        public readonly string $languageTag = ''
    ) {
    }

    public function encode(): string
    {
        return \pack(
            'CN2a*Na*',
            self::getNumber(),
            $this->reasonCode->value,
            \strlen($this->description), $this->description,
            \strlen($this->languageTag), $this->languageTag
        );
    }

    public static function decode(): \Generator
    {
        $reasonCode  = yield from Ssh\uint32();
        [$description, $languageTag] = yield from Ssh\times(2, Ssh\string(...));

        return new static(DisconnectReason::from($reasonCode), $description, $languageTag);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_DISCONNECT;
    }
}
