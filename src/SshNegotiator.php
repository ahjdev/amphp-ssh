<?php declare(strict_types=1);

namespace Amp\Ssh;


interface SshNegotiator
{
    public function negotiate(
        SshPacketHandler $binaryPacketHandler,
        string $serverIdentification,
        string $clientIdentification
    );
}
