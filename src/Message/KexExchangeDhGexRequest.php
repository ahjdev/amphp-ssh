<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
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

    public function encode(): string
    {
        return parent::encode() . pack('N3', $this->min, $this->ideal, $this->max);
    }

    public static function decode(): \Generator
    {
        [$min, $ideal, $max] = yield from Ssh\times(3, Ssh\uint32(...));

        return new self($min, $ideal, $max);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEX_DH_GEX_REQUEST;
    }
}
