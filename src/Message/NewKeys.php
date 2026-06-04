<?php

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class NewKeys extends SshMessage
{
    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::NEWKEYS;
    }
}
