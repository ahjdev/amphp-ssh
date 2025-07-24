<?php

namespace Amp\Ssh\Cryption;

use Amp\Ssh\SshCryption;

/**
 * @internal
 */
final class None implements SshCryption
{
    
    public function getName(): string
    {
        return 'none';
    }

    public function getKeySize(): int
    {
        return 0;
    }

    public function getBlockSize(): int
    {
        return 8;
    }

    public function crypt(string $payload): string
    {
        return $payload;
    }

    public function decrypt(string $payload): string
    {
        return $payload;
    }

    public function resetDecrypt(string $key, string $initIv): self
    {
        return $this;
    }

    public function resetEncrypt(string $key, string $initIv): self
    {
        return $this;
    }
}
