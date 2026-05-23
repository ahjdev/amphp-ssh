<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\ChannelRequest;

final class ChannelRequestWindowChange extends ChannelRequest
{
    public function __construct(
        int $recipientChannel,
        public readonly int $columns,
        public readonly int $rows,
        public readonly int $width,
        public readonly int $height,
    ) {
        parent::__construct($recipientChannel, false);
        $this->type = ChannelRequestType::WINDOW_CHANGE;
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . \pack('N4', $this->columns, $this->rows, $this->width, $this->height);
    }
}
