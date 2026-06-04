<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class Ignore extends SshMessage
{
    public function __construct(public readonly string $data)
    {
    }

    #[\Override]
    public function encode(): string
    {
        return \pack('CNa*', self::getNumber(), \strlen($this->data), $this->data);
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $data = $data->readString();
        return new static($data);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::IGNORE;
    }
}
