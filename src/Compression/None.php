<?php declare(strict_types=1);

namespace Amp\Ssh\Compression;

final class None extends SshCompression
{
    public function getName(): string
    {
        return 'none';
    }

    public function compress(string $data): string
    {
        return $data;
    }

    public function decompress(string $data): string
    {
        return $data;
    }

    public function reset(): void
    {
    }
}
