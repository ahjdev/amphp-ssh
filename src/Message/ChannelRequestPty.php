<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

final class ChannelRequestPty extends ChannelRequest
{
    public function __construct(
        int $recipientChannel,
        bool $wantReply = true,
        public readonly string $term = 'xterm',
        public readonly int $columns,
        public readonly int $rows,
        public readonly int $width,
        public readonly int $height,
        public readonly array $modes,
    ) {
        parent::__construct($recipientChannel, $wantReply);
        $this->type = ChannelRequestType::PTY;
    }

    public function encode(): string
    {
        $modes = '';

        foreach ($this->modes as $id => $value) {
            $modes .= \pack('CN', $id, $value);
        }

        $modes .= \pack('C', ChannelRequestPtyMode::TTY_OP_END);

        return parent::encode() . \pack(
            'Na*',
            \strlen($this->term), $this->term,
            $this->columns, $this->rows, $this->width, $this->height,
            \strlen($modes), $modes
        );
    }
}
