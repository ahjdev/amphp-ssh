<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use phpseclib3\Common\Functions\Strings;

abstract class Channel extends SshMessage
{
    public function __construct(public readonly int $recipientChannel)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack('CN', self::getNumber(), $this->recipientChannel);
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$recipientChannel] = Strings::unpackSSH2('N', $data);
        return new static($recipientChannel);
    }
}
