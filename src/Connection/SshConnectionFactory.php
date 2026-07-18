<?php declare(strict_types=1);

namespace Amp\Ssh\Connection;

use Amp\Ssh\Transport\SshPacketHandler;

interface SshConnectionFactory
{
    public function create(SshPacketHandler $packetHandler): SshConnection;
}
