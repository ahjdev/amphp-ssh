<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class Unimplemented extends SshMessage
{
    public function __construct(public readonly int $seqNumber)
    {
    }

    public function encode(): string
    {
        return \pack('CN', self::getNumber(), $this->seqNumber);
    }

    public static function decode(SshBinary $data): self
    {
        $seqNumber = $data->readInt();
        return new static($seqNumber);
    }

    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_UNIMPLEMENTED;
    }
}
