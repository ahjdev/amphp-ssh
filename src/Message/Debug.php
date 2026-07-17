<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class Debug extends SshMessage
{
    public function __construct(
        public readonly bool $alwaysDisplay,
        public readonly string $message,
        public readonly string $languageTag,
    ) {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack(
            'C2Na*Na*',
            self::getNumber(), $this->alwaysDisplay,
            \strlen($this->message), $this->message,
            \strlen($this->languageTag), $this->languageTag
        );
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$alwaysDisplay, $message, $languageTag] = Strings::unpackSSH2('bs2', $data);
        return new static($alwaysDisplay, $message, $languageTag);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::DEBUG;
    }
}
