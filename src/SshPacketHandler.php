<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshPacketHandler
{
    public function read(): SshMessage;

    public function write(string|SshMessage $message);
}
