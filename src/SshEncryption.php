<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshEncryption extends SshCryption
{
    public function crypt(string $payload): string;

    public function resetEncrypt(string $key, string $initIv): self;
}
