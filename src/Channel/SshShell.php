<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

interface SshShell extends SshChannel
{
    /**
     * Execute command
     */
    public function exec(string $command);

    /**
     * Starts the shell.
     */
    public function start(int $columns = 80, int $rows = 24, int $width = 800, int $height = 600): void;

    public function windowsSize(int $columns = 80, int $rows = 24, int $width = 800, int $height = 600): void;

}
