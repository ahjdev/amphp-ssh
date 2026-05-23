<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

class KeyExchangeDhInit extends SshMessage
{
    final public function __construct(public readonly string $exchange)
    {
    }

    #[\Override]
    final public function encode(): string
    {
        return \pack('CNa*', self::getNumber(), \strlen($this->exchange), $this->exchange);
    }

    #[\Override]
    final public static function decode(SshBinary $data): self
    {
        $exchange = $data->readString();
        return new static($exchange);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEXDH_INIT;
    }
}
