<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        $count = Strings::unpackSSH2('N', $data)[0];
        $extensions = [];
        for ($i = 0; $i < $count; $i++) {
            [$name, $value] = Strings::unpackSSH2('s2', $data);
            $extensions[$name] = $value;
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
