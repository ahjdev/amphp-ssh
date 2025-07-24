<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\Ssh\Transport\BinaryPacketHandler;

interface SshAuthentication
{
    public function authenticate(BinaryPacketHandler $handler, string $sessionId);
}
