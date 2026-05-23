<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class KexDhGexRequest extends SshMessage
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
    public static function decode(SshBinary $data): self
    {
        $min   = $data->readInt();
        $ideal = $data->readInt();
        $max   = $data->readInt();

        return new static($min, $ideal, $max);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEX_DH_GEX_REQUEST;
    }
}
