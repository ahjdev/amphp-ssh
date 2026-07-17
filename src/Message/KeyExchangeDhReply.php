<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

class KeyExchangeDhReply extends SshMessage
{
    final public function __construct(
        public readonly string $hostKey,
        public readonly string $fBytes,
        public readonly string $signature,
    ) {
        if (strlen($this->signature) < 4) {
            throw new \LengthException('The signature needs at least four bytes');
        }
    }

    #[\Override]
    final public function encode(): string
    {
        return \pack(
            'CNa*Na*Na*',
            self::getNumber(),
            \strlen($this->hostKey), $this->hostKey,
            \strlen($this->fBytes), $this->fBytes,
            \strlen($this->signature), $this->signature,
        );
    }

    #[\Override]
    final public static function decode(string $data): self
    {
        [$hostKey, $fBytes, $signature] = Strings::unpackSSH2('s3', $data);
        return new static($hostKey, $fBytes, $signature);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::KEX_ECDH_REPLY;
    }
}
