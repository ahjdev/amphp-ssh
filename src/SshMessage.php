<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\ForbidCloning;
use Amp\ForbidSerialization;

abstract class SshMessage implements \Stringable
{
    use ForbidCloning;
    use ForbidSerialization;

    abstract public static function getType(): SshMessageType;

    public static function decode(SshBinary $data): self
    {
        return new static();
    }

    public function encode(): string
    {
        return \pack('C', static::getNumber());
    }

    final static public function toNameList(array $value): string
    {
        return \implode(',', $value);
    }

    final static public function getNumber(): int
    {
        return self::getType()->getNumber();
    }

    final public function __toString(): string
    {
        return $this->encode();
    }
}
