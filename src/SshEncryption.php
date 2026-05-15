<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshEncryption
{
    public function crypt(string $payload): string;
}
