<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class GlobalRequest extends SshMessage
{
    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_GLOBAL_REQUEST;
    }
}
