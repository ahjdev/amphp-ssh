<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\ForbidCloning;
use Amp\ForbidSerialization;

abstract class SshMessage implements \Stringable
{
    use ForbidCloning;
    use ForbidSerialization;

    abstract public static function getNumber(): SshMessageType;

    final static public function toNameList(array $value): string
    {
        return \implode(',', $value);
    }

    final public function __toString(): string
    {
        return $this->encode();
    }

    public static function decode(): \Generator
    {
        $number = yield 2; // todo
        return new static();
    }

    public function encode(): string
    {
        return \pack('C', self::getNumber()->value);
    }
}
