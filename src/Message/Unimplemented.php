<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class Unimplemented extends SshMessage
{
    public function __construct(public readonly int $seqNumber)
    {
    }

    public function encode(): string
    {
        return \pack('CN', self::getNumber(), $this->seqNumber);
    }

    public static function decode(string $data): self
    {
        $seqNumber = Strings::unpackSSH2('N', $data)[0];
        return new static($seqNumber);
    }

    public static function getType(): SshMessageType
    {
        return SshMessageType::UNIMPLEMENTED;
    }
}
