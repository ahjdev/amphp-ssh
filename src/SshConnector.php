<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\Cancellation;
use Psr\Http\Message\UriInterface as PsrUri;

interface SshConnector
{
    public function connect(PsrUri|string $uri, SshAuthentication $authentication, ?Cancellation $cancellation = null, string $identification = 'SSH-2.0-AmpSSH_0.1'): SshResource;
}
