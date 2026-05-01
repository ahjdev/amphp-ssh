<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

final class ServiceAccept extends Service
{
    public static function getNumber(): int
    {
        return self::SSH_MSG_SERVICE_ACCEPT;
    }
}
