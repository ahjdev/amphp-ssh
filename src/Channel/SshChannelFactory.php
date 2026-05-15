<?php declare(strict_types=1);

namespace Amp\Ssh\Channel;

interface SshChannelFactory
{
    public function createShell(): SshShell;

    public function createProcess(): SshProcess;
}
