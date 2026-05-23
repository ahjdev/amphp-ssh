<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;

/**
 * @internal
 */
abstract class Service extends SshMessage
{
    public function __construct(public readonly string $name)
    {
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $name = $data->readString();
        return new static($name);
    }
}
