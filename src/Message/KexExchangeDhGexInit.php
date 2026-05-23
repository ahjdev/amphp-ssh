<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessageType;

final class KexExchangeDhGexInit extends KeyExchangeDhInit
{
    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEX_DH_GEX_INIT;
    }
}
