<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\DisconnectReason;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class Disconnect extends SshMessage
{
    public function __construct(
        public readonly DisconnectReason $reasonCode = DisconnectReason::BY_APPLICATION,
        public readonly string $description = '',
        public readonly string $languageTag = ''
    ) {
    }

    #[\Override]
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

    #[\Override]
    public static function decode(string $data): self
    {
        [$reasonCode, $description, $languageTag] = Strings::unpackSSH2('Ns2', $data);
        return new static(DisconnectReason::from($reasonCode), $description, $languageTag);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::DISCONNECT;
    }
}
