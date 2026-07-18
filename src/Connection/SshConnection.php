<?php declare(strict_types=1);

namespace Amp\Ssh\Connection;

use Amp\Closable;
use Amp\Ssh\Channel\SshProcess;
use Amp\Ssh\Channel\SshShell;

interface SshConnection extends Closable
{
    public function openShell(array $environment = []): SshShell;

    /**
     * Execute command
     */
    public function exec(string $command, ?string $workingDirectory = null, array $environment = []): SshProcess;
}
