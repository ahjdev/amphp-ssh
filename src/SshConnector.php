<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\Cancellation;
use Amp\Socket\SocketAddress;
use Amp\Socket\SocketConnector;
use Amp\Ssh\Authentication\SshAuthentication;
use Amp\Ssh\Connection\SshConnection;

interface SshConnector
{
    public function connect(SocketAddress|string $uri, SshAuthentication $authentication, ?SocketConnector $connector = null, ?Cancellation $cancellation = null, string $identification = 'SSH-2.0-AmpSSH_0.1'): SshConnection;
}
