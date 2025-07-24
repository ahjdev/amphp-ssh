<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshDecryption extends SshCryption
{
    public function decrypt(string $payload): string;

    public function resetDecrypt(string $key, string $initIv): self;
}
