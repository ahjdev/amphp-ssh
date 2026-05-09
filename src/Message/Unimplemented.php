<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class Unimplemented extends SshMessage
{
    public function __construct(public readonly int $seqNumber)
    {
    }

    public function encode(): string
    {
        return \pack('CN', self::getNumber()->value, $this->seqNumber);
    }

    public static function decode(): \Generator
    {
        $seqNumber = yield from Ssh\uint32();

        return new static($seqNumber);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_UNIMPLEMENTED;
    }
}
