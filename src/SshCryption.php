<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshCryption
{
    public function getName(): string;

    public function getKeySize(): int;

    public function getBlockSize(): int;

    public function crypt(string $payload): string;

    public function decrypt(string $payload): string;

    public function resetDecrypt(string $key, string $initIv): self;

    public function resetEncrypt(string $key, string $initIv): self;
}
