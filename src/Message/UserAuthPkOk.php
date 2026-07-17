<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        [$algorithm, $blob] = Strings::unpackSSH2('s2', $data);
        return new static($algorithm, $blob);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::USERAUTH_PK_OK;
    }
}
