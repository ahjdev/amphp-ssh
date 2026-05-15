<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

interface SshProcess extends SshChannel
{
    /**
     * Starts the process.
     */
    public function start(): void;

    /**
     * Returns the command to execute.
     *
     * @return string The command to execute.
     */
    public function getCommand(): string;
}
