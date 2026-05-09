<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class NewCompress extends SshMessage
{
    public function __construct(public readonly string $algorithm, public readonly bool $clientCanAccept = true)
    {
    }

    public function encode(): string
    {
        return \pack('CNa*C', self::getNumber()->value, \strlen($this->algorithm), $this->algorithm, $this->clientCanAccept);
    }

    public static function decode(): \Generator
    {
        $algorithm = yield from Ssh\string();
        $clientCanAccept = yield from Ssh\boolean();

        return new static($algorithm, $clientCanAccept);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_NEWCOMPRESS;
    }
}
