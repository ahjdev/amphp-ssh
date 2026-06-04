<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class UserAuthPkOk extends SshMessage
{
    public function __construct(public readonly string $algorithm, public readonly string $blob)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack(
            'C*Na*Na', self::getNumber(),
            \strlen($this->algorithm), $this->algorithm,
            \strlen($this->blob), $this->blob,
        );
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $algorithm = $data->readString();
        $blob      = $data->readString();

        return new static($algorithm, $blob);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::USERAUTH_PK_OK;
    }
}
