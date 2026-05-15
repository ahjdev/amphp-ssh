<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshDecryption
{
    public function decrypt(string $payload): string;
}
