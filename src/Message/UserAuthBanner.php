<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class UserAuthBanner extends SshMessage
{
    public function __construct(public readonly string $message, public readonly string $languageTag)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack(
            'C*Na*Na',
            self::getNumber(),
            \strlen($this->message), $this->message,
            \strlen($this->languageTag), $this->languageTag,
        );
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$message, $languageTag] = Strings::unpackSSH2('s2', $data);
        return new self($message, $languageTag);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::USERAUTH_BANNER;
    }
}
