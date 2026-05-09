<?php

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class NewKeys extends SshMessage
{
    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_NEWKEYS;
    }
}
