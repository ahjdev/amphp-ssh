<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class ExtInfo extends SshMessage implements \IteratorAggregate
{
    public function __construct(private readonly array $extensions = [])
    {
    }

    public function encode(): string
    {
        $payload  = parent::encode();
        $payload .= \pack('N', \count($this->extensions));

        foreach ($this->extensions as $name => $value) {
            $payload .= \pack('Na*Na*', \strlen($name), $name, \strlen($value), $value);
        }

        return $payload;
    }

    public static function decode(): \Generator
    {
        $count = yield from Ssh\uint32();
        $extensions = [];
        for ($i = 0; $i < $count; $i++) {
            [$name, $value] = yield from Ssh\times(2, Ssh\string(...));
            $extensions[$name] = $value;
        }

        return new static($extensions);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_EXT_INFO;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->extensions;
    }
}
