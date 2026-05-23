<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class UserAuthFailure extends SshMessage
{
    public function __construct(public readonly array $nextAuthentications, public readonly bool $partialSuccess)
    {
    }

    #[\Override]
    public function encode(): string
    {
        $nextAuthentications = $this->toNameList($this->nextAuthentications);
        return \pack(
            'C*Na*C',
            self::getNumber(),
            \strlen($nextAuthentications), $nextAuthentications,
            $this->partialSuccess,
        );
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $nextAuthentications = $data->readNamelist();
        $partialSuccess = $data->readBoolean();

        return new static($nextAuthentications, $partialSuccess);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_USERAUTH_FAILURE;
    }
}
