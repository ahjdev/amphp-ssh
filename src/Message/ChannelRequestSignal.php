<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\Signal;
use Amp\Ssh\Message\ChannelRequest;

final class ChannelRequestSignal extends ChannelRequest
{
    public readonly Signal $signal;

    public function __construct(int $recipientChannel, int|Signal $signal)
    {
        parent::__construct($recipientChannel, false);
        $this->type = ChannelRequestType::SIGNAL;
        $this->signal = \is_int($signal) ? Signal::fromCode($signal) : $signal;
    }

    #[\Override]
    public function encode(): string
    {
        $signal = $this->signal->value;
        return parent::encode() . \pack('Na*', \strlen($signal), $signal);
    }
}
