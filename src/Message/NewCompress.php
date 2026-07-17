<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

final class NewCompress extends SshMessage
{
    public function __construct(public readonly string $algorithm, public readonly bool $clientCanAccept = true)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack('CNa*C', self::getNumber(), \strlen($this->algorithm), $this->algorithm, $this->clientCanAccept);
    }

    #[\Override]
    public static function decode(string $data): self
    {
        [$algorithm, $clientCanAccept] = Strings::unpackSSH2('sb', $data);
        return new static($algorithm, $clientCanAccept);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::NEWCOMPRESS;
    }
}
