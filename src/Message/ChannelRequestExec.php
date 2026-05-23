<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;

final class ChannelRequestExec extends ChannelRequest
{
    public function __construct(int $recipientChannel, public readonly string $command, bool $wantReply = true)
    {
        parent::__construct($recipientChannel, $wantReply);
        $this->type = ChannelRequestType::EXEC;
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . \pack('Na*', \strlen($this->command), $this->command);
    }
}
