<?php declare(strict_types=1);

namespace Amp\Ssh;

use Revolt\EventLoop;
use Psr\Http\Message\UriInterface as PsrUri;
use Amp\Http\Client\HttpException;
use Amp\Cancellation;
use Amp\Socket\SocketConnector;
use Amp\Ssh\Authentication\SshAuthentication;
use Amp\Ssh\Connection\Rfc4253Connector;
use Amp\Ssh\Connection\SshConnectException;
use Amp\Ssh\Connection\SshConnection;

/**
 * Set or access the global websocket Connector instance.
 */
function sshConnector(?SshConnector $connector = null): SshConnector
{
    static $map;
    $map ??= new \WeakMap();
    $driver = EventLoop::getDriver();

    if ($connector) {
        return $map[$driver] = $connector;
    }

    return $map[$driver] ??= new Rfc4253Connector();
}

/**
 * @throws HttpException Thrown if the request fails.
 * @throws SshConnectException If the response received is invalid or is not a switching protocols (101) response.
 */
function connect(PsrUri|string $uri, SshAuthentication $authentication, ?SocketConnector $connector = null, ?Cancellation $cancellation = null, string $identification = "SSH-2.0-AmpSSH_0.1"): SshConnection
{
    return sshConnector()->connect($uri, $authentication, $connector, $cancellation, $identification);
}
