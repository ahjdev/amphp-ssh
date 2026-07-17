<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class UserAuthFailure extends SshMessage
{
    public function __construct(public readonly array $nextAuthentications, public readonly bool $partialSuccess)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return Strings::packSSH2('CLb', self::getNumber(), $this->nextAuthentications, $this->partialSuccess);
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$nextAuthentications, $partialSuccess] = Strings::unpackSSH2('Lb', $data);
        return new static($nextAuthentications, $partialSuccess);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::USERAUTH_FAILURE;
    }
}
