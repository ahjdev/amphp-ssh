<?php declare(strict_types=1);

namespace Amp\Ssh\Transport;

interface SshParserFactory
{
    public function create(SshPacketHandler $packetHandler): SshParser;
}
