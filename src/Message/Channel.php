<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

abstract class Channel extends SshMessage
{
    public function __construct(public readonly int $recipientChannel)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack('CN', self::getNumber(), $this->recipientChannel);
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $recipientChannel = $data->readInt();
        return new static($recipientChannel);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_CLOSE;
    }
}
