<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;

final class Debug extends SshMessage
{
    public function __construct(
        public readonly bool $alwaysDisplay,
        public readonly string $message,
        public readonly string $languageTag,
    ) {
    }

    public function encode(): string
    {
        return \pack(
            'C2Na*Na*',
            self::getNumber(), $this->alwaysDisplay,
            \strlen($this->message), $this->message,
            \strlen($this->languageTag), $this->languageTag
        );
    }

    public static function decode(): \Generator
    {
        $alwaysDisplay = yield from Ssh\boolean();
        [$message, $languageTag] = yield from Ssh\times(2, Ssh\string(...));

        return new static($alwaysDisplay, $message, $languageTag);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_DEBUG;
    }
}
