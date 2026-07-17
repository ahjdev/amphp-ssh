<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Crypt\Hash;

enum SshMacType: string
{
    case NONE = 'none';
    // SHA
    case HMAC_SHA1 = 'hmac-sha1';
    case HMAC_SHA1_96 = 'hmac-sha1-96';
    case HMAC_SHA2_256 = 'hmac-sha2-256';
    case HMAC_SHA2_512 = 'hmac-sha2-512';
    case HMAC_SHA1_ETM = 'hmac-sha1-etm@openssh.com';
    case HMAC_SHA1_96_ETM = 'hmac-sha1-96-etm@openssh.com';
    case HMAC_SHA2_256_ETM = 'hmac-sha2-256-etm@openssh.com';
    case HMAC_SHA2_512_ETM = 'hmac-sha2-512-etm@openssh.com';

    // Umac
    case UMAC_64  = 'umac-64@openssh.com';
    case UMAC_128 = 'umac-128@openssh.com';
    case UMAC_64_ETM  = 'umac-64-etm@openssh.com';
    case UMAC_128_ETM = 'umac-128-etm@openssh.com';

    // MD5
    case HMAC_MD5 = 'hmac-md5';
    case HMAC_MD5_96 = 'hmac-md5-96';
    case HMAC_MD5_ETM = 'hmac-md5-etm@openssh.com';
    case HMAC_MD5_96_ETM = 'hmac-md5-96-etm@openssh.com';

    public function getLength(bool $bits = false): int
    {
        // algo_96 = 12 but we use phpseclib
        $bytes = match ($this) {
            self::NONE => 0,
            self::UMAC_64,
            self::UMAC_64_ETM => 8,
            // 16
            self::HMAC_MD5,
            self::HMAC_MD5_96,
            self::HMAC_MD5_ETM,
            self::HMAC_MD5_96_ETM,
            self::UMAC_128,
            self::UMAC_128_ETM => 16,
            // 20
            self::HMAC_SHA1,
            self::HMAC_SHA1_ETM,
            self::HMAC_SHA1_96,
            self::HMAC_SHA1_96_ETM => 20,
            // 32
            self::HMAC_SHA2_256,
            self::HMAC_SHA2_256_ETM => 32,
            // 64
            self::HMAC_SHA2_512,
            self::HMAC_SHA2_512_ETM => 64,
        };
        return $bytes << ($bits ? 3 : 0);
    }

    public function getHashAlgorithm(): ?string
    {
        return match ($this) {
            self::NONE => null,
            // umac-64
            self::UMAC_64,
            self::UMAC_64_ETM => 'umac-64',
            // umac-128
            self::UMAC_128,
            self::UMAC_128_ETM => 'umac-128',
            // sha256
            self::HMAC_SHA2_256,
            self::HMAC_SHA2_256_ETM => 'sha256',
            // sha512
            self::HMAC_SHA2_512,
            self::HMAC_SHA2_512_ETM => 'sha512',
            // md5
            self::HMAC_MD5,
            self::HMAC_MD5_ETM => 'md5',
            self::HMAC_MD5_96,
            self::HMAC_MD5_96_ETM => 'md5-96',
            // sha1
            self::HMAC_SHA1,
            self::HMAC_SHA1_ETM => 'sha1',
            self::HMAC_SHA1_96,
            self::HMAC_SHA1_96_ETM => 'sha1-96',
        };
    }

    public function resolve(): ?Hash
    {
        if ($this === self::NONE) {
            return null;
        }

        return new Hash($this->getHashAlgorithm());
    }

    public function isEtm(): bool
    {
        return match ($this) {
            self::UMAC_64_ETM,
            self::UMAC_128_ETM,
            self::HMAC_MD5_ETM,
            self::HMAC_MD5_96_ETM,
            self::HMAC_SHA1_ETM,
            self::HMAC_SHA1_96_ETM,
            self::HMAC_SHA2_256_ETM,
            self::HMAC_SHA2_512_ETM => true,
            default => false,
        };
    }

    public function isUmac(): bool
    {
        return match ($this) {
            self::UMAC_64,
            self::UMAC_128,
            self::UMAC_64_ETM,
            self::UMAC_128_ETM => true,
            default => false,
        };
    }

    /**
     * @return list<string>
     */
    public static function getSupported(): array
    {
        return [
            'hmac-sha2-256-etm@openssh.com',
            'hmac-sha2-512-etm@openssh.com',
            'hmac-sha1-96-etm@openssh.com',
            'hmac-sha1-etm@openssh.com',
            'hmac-sha2-256',
            'hmac-sha2-512',
            'hmac-sha1-96',
            'hmac-sha1',
            'hmac-md5-96-etm@openssh.com',
            'hmac-md5-96',
            'hmac-md5-etm@openssh.com',
            'hmac-md5',
            'umac-64-etm@openssh.com',
            'umac-128-etm@openssh.com',
            'umac-64@openssh.com',
            'umac-128@openssh.com',
            'none',
        ];
    }
}
