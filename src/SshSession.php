<?php declare(strict_types=1);

namespace Amp\Ssh;

use Amp\Cancellation;
use Amp\ByteStream\WritableStream;
use Amp\ByteStream\ReadableStream;
use Amp\Closable;

interface SshSession extends Closable
{
    /**
     * Starts a new session.
     */
    public function start(): void;

    /**
     * Wait for the session to end.
     *
     * @return int The process exit code.
     */
    public function join(?Cancellation $cancellation = null): int;

    /**
     * Forcibly end the session.
     */
    public function kill(): void;

    /**
     * Send a signal to the session.
     *
     * @param int $signo Signal number to send to session.
     *
     * @throws SshException If signal sending is not supported.
     */
    public function signal(int $signo): void;

    /**
     * Gets the current working directory.
     *
     * @return string The working directory.
     */
    public function getWorkingDirectory(): string;

    /**
     * Gets the environment variables array.
     *
     * @return array<string,string> Array of environment variables.
     */
    public function getEnvironment(): array;

    /**
     * Determines if the session is still running.
     */
    public function isRunning(): bool;

    /**
     * Gets the session input stream (STDIN).
     */
    public function getStdin(): WritableStream;

    /**
     * Gets the session output stream (STDOUT).
     */
    public function getStdout(): ReadableStream;

    /**
     * Gets the session error stream (STDERR).
     */
    public function getStderr(): ReadableStream;
}
