<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;

final class Ignore extends SshMessage
{
    public function __construct(public readonly string $data)
    {
    }

    public function encode(): string
    {
        return \pack('CNa*', self::getNumber(), \strlen($this->data), $this->data);
    }

    public static function decode(): \Generator
    {
        $data = yield from Ssh\string();

        return new static($data);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_IGNORE;
    }
}
