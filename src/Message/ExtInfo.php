<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class ExtInfo extends SshMessage implements \IteratorAggregate
{
    public function __construct(public readonly array $extensions = [])
    {
    }

    #[\Override]
    public function encode(): string
    {
        $payload  = parent::encode();
        $payload .= \pack('N', \count($this->extensions));

        foreach ($this->extensions as $name => $value) {
            $payload .= \pack('Na*Na*', \strlen($name), $name, \strlen($value), $value);
        }

        return $payload;
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $count = $data->readInt();
        $extensions = [];
        for ($i = 0; $i < $count; $i++) {
            $extensions[$data->readString()] = $data->readString();
        }

        return new static($extensions);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::EXT_INFO;
    }

    public function getIterator(): \Traversable
    {
        yield from $this->extensions;
    }
}
