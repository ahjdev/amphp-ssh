<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use phpseclib3\Common\Functions\Strings;

/**
 * @internal
 */
abstract class Service extends SshMessage
{
    public function __construct(public readonly string $name)
    {
    }

    #[\Override]
    public static function decode(string $data): self
    {
        $name = Strings::unpackSSH2('s', $data)[0];
        return new static($name);
    }
}
