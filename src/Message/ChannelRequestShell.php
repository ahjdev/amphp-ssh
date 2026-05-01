<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;
use Amp\Ssh\Message\ChannelRequest;

final class ChannelRequestShell extends ChannelRequest
{
    public function __construct(int $recipientChannel, bool $wantReply = true)
    {
        parent::__construct($recipientChannel, $wantReply);
        $this->type = ChannelRequestType::SHELL;
    }
}
