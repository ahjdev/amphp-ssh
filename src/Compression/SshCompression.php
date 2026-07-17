<?php declare(strict_types=1);

namespace Amp\Ssh\Compression;

use Amp\Ssh\Compression\None;
use Amp\Ssh\Compression\Zlib;

abstract class SshCompression
{
    /** @var list<string> */
    private static array $supported;

    abstract public function reset(): void;

    abstract public function getName(): string;

    abstract public function compress(string $data): string;

    abstract public function decompress(string $data): string;

    public static function from(string $value): static
    {
        return match ($value) {
            'none' => new None,
            'zlib' => new Zlib,
            'zlib@openssh.com' => new Zlib(true),
        };
    }

    /**
     * @return list<string>
     */
    public static function getSupported(): array
    {
        return self::$supported ??= ['none', ...(Zlib::isSupported() ? ['zlib@openssh.com', 'zlib'] : [])];
    }
}
