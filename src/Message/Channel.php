<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;

abstract class Channel extends SshMessage
{
    public function __construct(public readonly int $recipientChannel)
    {
    }

    public function encode(): string
    {
        return \pack('CN', self::getNumber(), $this->recipientChannel);
    }

    public static function decode(): \Generator
    {
        $channel = yield from Ssh\uint32();

        return new static($channel);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_CHANNEL_CLOSE;
    }
}
