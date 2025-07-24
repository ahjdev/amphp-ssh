<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshProcess extends SshSession
{
    /**
     * Returns the command to execute.
     *
     * @return string The command to execute.
     */
    public function getCommand(): string;
}
