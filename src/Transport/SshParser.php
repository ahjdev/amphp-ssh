<?php declare(strict_types=1);

namespace Amp\Ssh\Transport;

interface SshParser
{
    /**
     * Parse ssh packets from peer data, invoking {@see SshPacketHandler::handlePacket()} for each packets.
     *
     * @throws SshParserException
     */
    public function push(string $data): void;

    /**
     * Cancel parsing and free any associated resources.
     */
    public function cancel(): void;
}
