<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class KexExchangeDhGexRequest extends SshMessage
{
    public function __construct(
        public readonly int $min = 2048,
        public readonly int $ideal = 4096,
        public readonly int $max = 8192,
    ) {
    }

    #[\Override]
    public function encode(): string
    {
        return parent::encode() . pack('N3', $this->min, $this->ideal, $this->max);
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$min, $ideal, $max] = Strings::unpackSSH2('N3', $data);
        return new static($min, $ideal, $max);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::KEXDH_GEX_REQUEST;
    }
}
