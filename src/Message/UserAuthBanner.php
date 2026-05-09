<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class UserAuthBanner extends SshMessage
{
    public function __construct(public readonly string $message, public readonly string $languageTag)
    {
    }

    public function encode(): string
    {
        return \pack(
            'C*Na*Na',
            self::getNumber()->value,
            \strlen($this->message), $this->message,
            \strlen($this->languageTag), $this->languageTag,
        );
    }

    public static function decode(): \Generator
    {
        [$message, $languageTag] = yield from Ssh\times(2, Ssh\string(...));

        return new self($message, $languageTag);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_USERAUTH_BANNER;
    }
}
