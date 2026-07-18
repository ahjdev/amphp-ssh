<?php declare(strict_types=1);

namespace Amp\Ssh\Authentication;

use Amp\Ssh\Authentication\SshAuthenticationFailureException;
use Amp\Ssh\Message\ServiceAccept;
use Amp\Ssh\Message\ServiceRequest;
use Amp\Ssh\Transport\SshPacketHandler;

abstract class SshAuthentication
{
    abstract public function authenticate(SshPacketHandler $packetHandler);

    public function __construct(#[\SensitiveParameter] protected readonly string $username)
    {
    }

    protected function requestService(SshPacketHandler $packetHandler)
    {
        if ($packetHandler->isAuthenticated()) {
            throw new SshAuthenticationFailureException('Already authenticated');
        }

        $serviceName = 'ssh-userauth';
        $packet  = new ServiceRequest($serviceName);
        $request = $packetHandler->writeMessage($packet);

        if (!$request instanceof ServiceAccept) {
            throw new SshAuthenticationFailureException('Authentication failure invalid response');
        }

        if ($request->name !== $serviceName) {
            throw new SshAuthenticationFailureException('Authentication service name mismatch (' . $serviceName . ' vs ' . $request->name . ')');
        }
    }
}
