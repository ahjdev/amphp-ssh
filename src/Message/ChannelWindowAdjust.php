<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;

final class ChannelWindowAdjust extends Channel
{
    public function __construct(int $recipientChannel, public readonly int $windowIncrement)
    {
        parent::__construct($recipientChannel);
    }

    public function encode(): string
    {
        return parent::encode() . \pack('N', $this->windowIncrement);
    }

    public static function decode(): \Generator
    {
        [$channel, $bytesToAdd] = yield from Ssh\times(2, Ssh\uint32(...));

        return new static($channel, $bytesToAdd);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_WINDOW_ADJUST;
    }
}
