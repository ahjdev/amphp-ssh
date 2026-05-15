<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

use Amp\ByteStream\ReadableIterableStream;
use Amp\ByteStream\ReadableResourceStream;
use Amp\ByteStream\ReadableStream;
use Amp\ByteStream\WritableIterableStream;
use Amp\ByteStream\WritableResourceStream;
use Amp\ByteStream\WritableStream;
use Amp\Cancellation;
use Amp\DeferredFuture;
use Amp\Future;
use Amp\Ssh\SshException;
use Amp\Ssh\Channel\SshChannel;

abstract class Rfc4254Channel implements SshChannel
{
    /**
     * @var DeferredFuture<int>
     */
    public readonly DeferredFuture $joinDeferred;

    /**
     * @param array<string,string> $env
     */
    public function __construct(
        private readonly int $localChannelId,
        private readonly int $remoteChannelId,
        private int $remoteWindowSize,
        private int $remoteMaxPacketSize,
        private array $env = [],
        private WritableStream $stdin,
        private ReadableStream $stdout,
        private ReadableStream $stderr,
    ) {
        $this->joinDeferred = new DeferredFuture;
    }

    public function __destruct()
    {
        if ($this->joinDeferred->isComplete()) {
            return;
        }

        $this->kill();
    }

    public function join(?Cancellation $cancellation = null): int
    {
        return $this->joinDeferred->getFuture()->await($cancellation);
    }

    public function kill(): void
    {
        if (!$this->isRunning()) {
            return;
        }
    
        $this->signal(\SIGKILL);
    }

    public function signal(int $signo): void
    {
        if (!$this->isRunning()) {
            return;
        }

        $this->session->signal($signo);
    }

    public function getEnvironment(): array
    {
        return $this->env;
    }

    public function isRunning(): bool
    {
        return !$this->joinDeferred->isComplete();
    }

    public function getStdin(): WritableResourceStream
    {
        return $this->stdin;
    }

    public function getStdout(): ReadableResourceStream
    {
        return $this->stdout;
    }

    public function getStderr(): ReadableResourceStream
    {
        return $this->stderr;
    }
}
