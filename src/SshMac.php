<?php declare(strict_types=1);

namespace Amp\Ssh;

enum SshMac: string
{
    case NONE = 'none';

    // SHA
    case HMAC_SHA1 = 'hmac-sha1';
    case HMAC_SHA1_96 = 'hmac-sha1-96';
    case HMAC_SHA2_256 = 'hmac-sha2-256';
    case HMAC_SHA2_512 = 'hmac-sha2-512';
    case HMAC_SHA1_ETM = 'hmac-sha1-etm@openssh.com';
    case HMAC_SHA2_256_ETM = 'hmac-sha2-256-etm@openssh.com';
    case HMAC_SHA2_512_ETM = 'hmac-sha2-512-etm@openssh.com';

    // MD5
    case HMAC_MD5 = 'hmac-md5';
    case HMAC_MD5_96 = 'hmac-md5-96';
    case HMAC_MD5_ETM = 'hmac-md5-etm@openssh.com';

    public function getLength(): int
    {
        return match ($this) {
            self::NONE => 0,
            self::HMAC_SHA1_96,  self::HMAC_MD5_96  => 12,
            self::HMAC_MD5,      self::HMAC_MD5_ETM => 16,
            self::HMAC_SHA1,     self::HMAC_SHA1_ETM => 20,
            self::HMAC_SHA2_256, self::HMAC_SHA2_256_ETM => 32,
            self::HMAC_SHA2_512, self::HMAC_SHA2_512_ETM => 64,
        };
    }

    public function getHashAlgorithm(): string
    {
        return match ($this) {
            self::NONE => 'none',
            self::HMAC_SHA2_256, self::HMAC_SHA2_256_ETM => 'sha256',
            self::HMAC_SHA2_512, self::HMAC_SHA2_512_ETM => 'sha512',
            self::HMAC_MD5,  self::HMAC_MD5_96,  self::HMAC_MD5_ETM => 'md5',
            self::HMAC_SHA1, self::HMAC_SHA1_96, self::HMAC_SHA1_ETM => 'sha1',
        };
    }

    public function isEtm(): bool
    {
        return $this === self::HMAC_MD5_ETM || $this === self::HMAC_SHA1_ETM ||
               $this === self::HMAC_SHA2_256_ETM || $this === self::HMAC_SHA2_512_ETM;
    }

    public function hash(string $data, #[\SensitiveParameter] $key): string
    {
        if ($this === self::NONE) {
            return '';
        }

        $hash = \hash_hmac($this->getHashAlgorithm(), $data, $key, true);

        if ($this === self::HMAC_SHA1_96 || $this === self::HMAC_MD5_96) {
            return \substr($hash, 0, 12);
        }

        return $hash;
    }

    public function compute(string $packet, int $seqNumber, string $key): string
    {
        if ($this === self::NONE) {
            return '';
        }

        $data = pack('N2a*', $seqNumber, \strlen($packet), $packet);
        return $this->hash($data, $key);
    }

    public function verify(string $packet, string $mac, int $seqNumber, string $key)
    {
        $expected = $this->compute($packet, $seqNumber, $key);

        return \hash_equals($expected, $mac);
    }
}
