<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\Signal;
use Amp\Ssh\Message\ChannelRequest;

final class ChannelRequestExitSignal extends ChannelRequest
{
    public readonly Signal $signal;

    public function __construct(
        int $recipientChannel,
        int|Signal $signal,
        public readonly bool $coreDumped,
        public readonly string $errorMessage,
        public readonly string $languageTag,
        bool $wantReply = true
    ) {
        parent::__construct($recipientChannel, $wantReply);
        $this->type = ChannelRequestType::EXIT_SIGNAL;
        $this->signal = \is_int($signal) ? Signal::fromCode($signal) : $signal;
    }

    #[\Override]
    public function encode(): string
    {
        $signal = $this->signal->value;

        return parent::encode() . \pack(
            'Na*CNa*Na*',
            \strlen($signal), $signal,
            $this->coreDumped,
            \strlen($this->errorMessage), $this->errorMessage,
            \strlen($this->languageTag),  $this->languageTag,
        );
    }
}
