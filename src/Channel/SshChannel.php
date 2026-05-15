<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

use Amp\ByteStream\ReadableStream;
use Amp\ByteStream\WritableStream;
use Amp\Cancellation;
use Amp\Ssh\SshException;

interface SshChannel
{
    /**
     * Wait for the channel to end.
     *
     * @return int The process exit code.
     */
    public function join(?Cancellation $cancellation = null): int;

    /**
     * Forcibly end the channel.
     */
    public function kill(): void;

    /**
     * Send a signal to the channel.
     *
     * @param int $signo Signal number to send to channel.
     *
     * @throws SshException If signal sending is not supported.
     */
    public function signal(int $signo): void;

    /**
     * Gets the environment variables array.
     *
     * @return array<string,string> Array of environment variables.
     */
    public function getEnvironment(): array;

    /**
     * Determines if the channel is still running.
     */
    public function isRunning(): bool;

    /**
     * Gets the channel input stream (STDIN).
     */
    public function getStdin(): WritableStream;

    /**
     * Gets the channel output stream (STDOUT).
     */
    public function getStdout(): ReadableStream;

    /**
     * Gets the channel error stream (STDERR).
     */
    public function getStderr(): ReadableStream;
}
