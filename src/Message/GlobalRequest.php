<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;

final class GlobalRequest extends SshMessage
{
    public static function getNumber(): int
    {
        return self::SSH_MSG_GLOBAL_REQUEST;
    }
}
