<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

use Amp\Process\Process;
use Amp\Ssh\Channel\SshProcess;
use Amp\DeferredFuture;
use Amp\ByteStream\WritableStream;
use Amp\ByteStream\ReadableStream;

final class Rfc4254Process extends Rfc4254Channel implements SshProcess
{
    private string $command;

    public function __construct(
        DeferredFuture $joinDeferred,
        int $localChannelId,
        int $remoteChannelId,
        int $remoteWindowSize,
        int $remoteMaxPacketSize,
        WritableStream $stdin,
        ReadableStream $stdout,
        ReadableStream $stderr,
        string $command,
        ?string $cwd = null,
        array $env = [],
    ) {
        parent::__construct(
            $joinDeferred, $localChannelId,$remoteChannelId, $remoteWindowSize, $remoteMaxPacketSize,
            $env, $stdin, $stdout, $stderr, 
        );
        $this->command = $cwd !== null ? \sprintf('cd %s; %s', $cwd, $command) : $command;
    }

    public function start(): void
    {

    }

    public function getCommand(): string
    {
        return $this->command;
    }
}
