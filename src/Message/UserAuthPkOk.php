<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh;

final class UserAuthPkOk extends SshMessage
{
    public function __construct(public readonly string $algorithm, public readonly string $blob)
    {
    }

    public function encode(): string
    {
        return \pack(
            'C*Na*Na', self::getNumber(),
            \strlen($this->algorithm), $this->algorithm,
            \strlen($this->blob), $this->blob,
        );
    }

    public static function decode(): \Generator
    {
        [$algorithm, $blob] = yield from Ssh\times(2, Ssh\string(...));

        return new static($algorithm, $blob);
    }

    public static function getNumber(): int
    {
        return self::SSH_MSG_USERAUTH_PK_OK;
    }
}
