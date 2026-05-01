<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\Channel;

final class ChannelExtendedData extends Channel
{
    const SSH_EXTENDED_DATA_STDERR = 1;

    public function __construct(int $recipientChannel, public readonly int $type, public readonly string $data)
    {
        parent::__construct($recipientChannel);
    }

    public function encode(): string
    {
        return parent::encode() . \pack('N2a*', $this->type, \strlen($this->data), $this->data);
    }

    public static function decode(): \Generator
    {
        [$channel, $type] = yield from Ssh\times(2, Ssh\uint32(...));
        $data = yield from Ssh\string();

        return new static($channel, $type, $data);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_CHANNEL_EXTENDED_DATA;
    }
}
