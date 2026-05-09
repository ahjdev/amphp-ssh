<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class Ignore extends SshMessage
{
    public function __construct(public readonly string $data)
    {
    }

    public function encode(): string
    {
        return \pack('CNa*', self::getNumber()->value, \strlen($this->data), $this->data);
    }

    public static function decode(): \Generator
    {
        $data = yield from Ssh\string();

        return new static($data);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_IGNORE;
    }
}
