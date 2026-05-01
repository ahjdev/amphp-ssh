<?php

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;

final class NewKeys extends SshMessage
{
    public static function getNumber(): int
    {
        return self::SSH_MSG_NEWKEYS;
    }
}
