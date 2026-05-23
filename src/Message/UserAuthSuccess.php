<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class UserAuthSuccess extends SshMessage
{
    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_USERAUTH_SUCCESS;
    }
}
