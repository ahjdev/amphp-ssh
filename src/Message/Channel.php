<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

abstract class Channel extends SshMessage
{
    public function __construct(public readonly int $recipientChannel)
    {
    }

    public function encode(): string
    {
        return \pack('CN', self::getNumber()->value, $this->recipientChannel);
    }

    public static function decode(): \Generator
    {
        $channel = yield from Ssh\uint32();

        return new static($channel);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_CLOSE;
    }
}
