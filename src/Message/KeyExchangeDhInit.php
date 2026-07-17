<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    final public static function decode(string $data): self
    {
        $exchange = Strings::unpackSSH2('s', $data)[0];
        return new static($exchange);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::KEX_ECDH_INIT;
    }
}
