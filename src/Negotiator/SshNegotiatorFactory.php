<?php declare(strict_types=1);

namespace Amp\Ssh\Negotiator;

use Amp\Ssh\Message\KeyExchangeInit;
use Amp\Ssh\Transport\SshPacketHandler;

interface SshNegotiatorFactory
{
    public function createKeyExchange(): KeyExchangeInit;
    public function create(SshPacketHandler $packetHandler, KeyExchangeInit $clientKex, KeyExchangeInit $serverKex): SshNegotiator;
}
