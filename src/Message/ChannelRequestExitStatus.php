<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\ChannelRequest;

final class ChannelRequestExitStatus extends ChannelRequest
{
    public function __construct(int $recipientChannel, public readonly int $code)
    {
        parent::__construct($recipientChannel, false);
        $this->type = ChannelRequestType::EXIT_STATUS;
    }

    public function encode(): string
    {
        return parent::encode() . \pack('N', $this->code);
    }
}
