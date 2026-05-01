<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\ChannelRequestType;

final class ChannelRequestEnv extends ChannelRequest
{
    public function __construct(int $recipientChannel, bool $wantReply = true, public readonly string $name, public readonly string $value)
    {
        parent::__construct($recipientChannel, $wantReply);
        $this->type = ChannelRequestType::ENV;
    }

    public function encode(): string
    {
        return parent::encode() . \pack('Na*Na*', \strlen($this->name), $this->name, \strlen($this->value), $this->value);
    }
}
