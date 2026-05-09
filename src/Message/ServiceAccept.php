<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessageType;

final class ServiceAccept extends Service
{
    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_SERVICE_ACCEPT;
    }
}
