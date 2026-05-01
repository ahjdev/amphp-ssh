<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;

final class KexExchangeDhGexInit extends SshMessage
{
    public function __construct(public readonly string $exchange)
    {
    }

    public function encode(): string
    {
        return \pack('CNa*', self::getNumber(), \strlen($this->exchange), $this->exchange);
    }

    public static function decode(): \Generator
    {
        $exchange = yield from Ssh\string();

        return new static($exchange);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_KEX_DH_GEX_INIT;
    }
}
