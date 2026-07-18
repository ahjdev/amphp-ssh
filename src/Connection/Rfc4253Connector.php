<?php declare(strict_types=1);

namespace Amp\Ssh\Connection;

use Amp\Cancellation;
use Amp\Socket\SocketAddress;
use Amp\Socket\SocketConnector;
use Amp\Ssh\Authentication\SshAuthentication;
use Amp\Ssh\Channel\Rfc4254ChannelFactory;
use Amp\Ssh\Channel\SshChannelFactory;
use Amp\Ssh\SshConnector;
use Amp\Ssh\Negotiator\Rfc4253NegotiatorFactory;
use Amp\Ssh\Negotiator\SshNegotiatorFactory;
use Amp\Ssh\Transport\Rfc4253CompilerFactory;
use Amp\Ssh\Transport\Rfc4253PacketHandler;
use Amp\Ssh\Transport\Rfc4253ParserFactory;
use Amp\Ssh\Transport\SshCompilerFactory;
use Amp\Ssh\Transport\SshParserFactory;

use function Amp\Socket\socketConnector;

final class Rfc4253Connector implements SshConnector
{
    public function __construct(
        private readonly SshConnectionFactory $connectionFactory = new Rfc4254ConnectionFactory,
        private readonly SshParserFactory     $parserFactory     = new Rfc4253ParserFactory,
        private readonly SshCompilerFactory   $compilerFactory   = new Rfc4253CompilerFactory,
        private readonly SshChannelFactory    $channelFactory    = new Rfc4254ChannelFactory,
        private readonly SshNegotiatorFactory $negotiatorFactory = new Rfc4253NegotiatorFactory,
    ) {
    }

    public function connect(SocketAddress|string $uri, SshAuthentication $authentication, ?SocketConnector $connector = null, ?Cancellation $cancellation = null, string $identification = 'SSH-2.0-AmpSSH_0.1'): SshConnection
    {
        $connector ??= socketConnector();
        $socket = $connector->connect($uri, cancellation: $cancellation);
        $packetHandler = new Rfc4253PacketHandler(
            $socket, $identification, $authentication,
            $this->parserFactory,
            $this->compilerFactory,
            $this->channelFactory,
            $this->negotiatorFactory
        );
        return $this->connectionFactory->create($packetHandler);
    }
}
