<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;
use Amp\Ssh\SshMessageType;

final class ChannelData extends Channel
{
    public function __construct(int $recipientChannel, public readonly string $data)
    {
        parent::__construct($recipientChannel);
    }

    public function encode(): string
    {
        return parent::encode() . \pack('Na*', \strlen($this->data), $this->data);
    }

    public static function decode(): \Generator
    {
        $channel = yield from Ssh\uint32();
        $data    = yield from Ssh\string();

        return new static($channel, $data);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_CHANNEL_DATA;
    }
}
