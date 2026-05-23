<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

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

    public static function decode(SshBinary $data): self
    {
        $prime = $data->readString();
        $generator = $data->readString();

        return new self($prime, $generator);
    }

    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEX_DH_GEX_GROUP;
    }
}
