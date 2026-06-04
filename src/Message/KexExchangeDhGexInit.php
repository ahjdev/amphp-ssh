<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessageType;

final class KexExchangeDhGexInit extends KeyExchangeDhInit
{
    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::KEXDH_GEX_INIT;
    }
}
