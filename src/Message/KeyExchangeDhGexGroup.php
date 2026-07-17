<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class KeyExchangeDhGexGroup extends SshMessage
{
    public function __construct(public readonly string $prime, public readonly string $generator)
    {
    }

    public function encode(): string
    {
        return parent::encode() . \pack(
            'Na*Na*',
            \strlen($this->prime), $this->prime,
            \strlen($this->generator), $this->generator
        );
    }

    public static function decode(string $data): self
    {
        [$prime, $generator] = Strings::unpackSSH2('s2', $data);
        return new self($prime, $generator);
    }

    public static function getType(): SshMessageType
    {
        return SshMessageType::KEXDH_GEX_GROUP;
    }
}
