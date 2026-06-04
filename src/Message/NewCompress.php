<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

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
    public static function decode(SshBinary $data): self
    {
        $algorithm = $data->readString();
        $clientCanAccept = $data->readBoolean();

        return new static($algorithm, $clientCanAccept);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::NEWCOMPRESS;
    }
}
