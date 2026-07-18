<?php declare(strict_types=1);

namespace Amp\Ssh\Connection;

use Amp\Ssh\Transport\SshPacketHandler;

final class Rfc4254ConnectionFactory implements SshConnectionFactory
{
    #[\Override]
    public function create(SshPacketHandler $packetHandler): Rfc4254Connection
    {
        return new Rfc4254Connection($packetHandler);
    }
}
