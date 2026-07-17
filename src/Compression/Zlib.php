<?php declare(strict_types=1);

namespace Amp\Ssh\Compression;

use Amp\Ssh\SshException;

final class Zlib extends SshCompression
{
    private ?\DeflateContext $deflate = null;
    private ?\InflateContext $inflate = null;

    public function __construct(private bool $deferred = false)
    {
    }

    public static function isSupported(): bool
    {
        return \function_exists('deflate_init');
    }

    public function isDeferred(): bool
    {
        return $this->deferred;
    }

    public function getName(): string
    {
        return $this->deferred ? 'zlib@openssh.com' : 'zlib';
    }

    public function reset(): void
    {
        $this->deflate = null;
        $this->inflate = null;
    }

    public function compress(string $data): string
    {
        $header = '';
        if ($this->deflate === null) {
            $header = "\x78\x9C";
            $this->deflate = \deflate_init(\ZLIB_ENCODING_RAW, ['window' => 15]);
        }

        if (($compressed = \deflate_add($this->deflate, $data, \ZLIB_PARTIAL_FLUSH)) === false) {
            throw new SshException('Compression failed');
        }

        return $header . $compressed;
    }

    public function decompress(string $data): string
    {
        if ($this->inflate === null) {
            $cmf = ord($data[0]);
            $cm = $cmf & 0x0F;
            if ($cm != 8) {
                // deflate
                throw new SshException("Only CM = 8 ('deflate') is supported ($cm)");
            }
            $cinfo = ($cmf & 0xF0) >> 4;
            if ($cinfo > 7) {
                throw new SshException("CINFO above 7 is not allowed ($cinfo)");
            }
            // $windowSize = 1 << ($cinfo + 8);
            $flg = ord($data[1]);
            //$fcheck = $flg && 0x0F;
            if ((($cmf << 8) | $flg) % 31) {
                throw new SshException('fcheck failed');
            }
            // $fdict = boolval($flg & 0x20);
            // $flevel = ($flg & 0xC0) >> 6;
            $this->inflate = \inflate_init(\ZLIB_ENCODING_RAW, ['window' => $cinfo + 8]);
            $data = \substr($data, 2);
        }

        if (($decompressed = \inflate_add($this->inflate, $data, \ZLIB_PARTIAL_FLUSH)) === false) {
            throw new SshException('Decompression failed');
        }

        return $decompressed;
    }
}
