<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;

/**
 * @internal
 */
abstract class Service extends SshMessage
{
    public function __construct(public readonly string $name)
    {
    }

    public static function decode(): \Generator
    {
        $name = yield from Ssh\string();

        return new static($name);
    }
}
