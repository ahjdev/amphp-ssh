<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Crypt\Blowfish;
use phpseclib3\Crypt\ChaCha20;
use phpseclib3\Crypt\Common\SymmetricKey;
use phpseclib3\Crypt\RC4;
use phpseclib3\Crypt\Rijndael;
use phpseclib3\Crypt\TripleDES;
use phpseclib3\Crypt\Twofish;

enum SshCipherType: string
{
    case NONE = 'none';
    case ARCFOUR = 'arcfour';
    case ARCFOUR128 = 'arcfour128';
    case ARCFOUR256 = 'arcfour256';
    case AES128_CBC = 'aes128-cbc';
    case AES192_CBC = 'aes192-cbc';
    case AES256_CBC = 'aes256-cbc';
    case AES128_CTR = 'aes128-ctr';
    case AES192_CTR = 'aes192-ctr';
    case AES256_CTR = 'aes256-ctr';
    case AES128_GCM = 'aes128-gcm@openssh.com';
    case AES256_GCM = 'aes256-gcm@openssh.com';
    case BLOWFISH_CBC = 'blowfish-cbc';
    case BLOWFISH_CTR = 'blowfish-ctr';
    case TRIPLE_DES_CBC = '3des-cbc';
    case TRIPLE_DES_CTR = '3des-ctr';
    case TWOFISH_CBC = 'twofish-cbc';
    case TWOFISH128_CBC = 'twofish128-cbc';
    case TWOFISH192_CBC = 'twofish192-cbc';
    case TWOFISH256_CBC = 'twofish256-cbc';
    case TWOFISH128_CTR = 'twofish128-ctr';
    case TWOFISH192_CTR = 'twofish192-ctr';
    case TWOFISH256_CTR = 'twofish256-ctr';
    case CHACHA20_POLY1305 = 'chacha20-poly1305@openssh.com';

    public function isAead(): bool
    {
        return match ($this) {
            self::AES128_GCM, self::AES256_GCM, self::CHACHA20_POLY1305 => true,
            default => false,
        };
    }

    public function usesIv(): bool
    {
        return match ($this) {
            self::TWOFISH_CBC,
            self::BLOWFISH_CBC,
            self::BLOWFISH_CTR,
            self::TRIPLE_DES_CBC,
            self::TRIPLE_DES_CTR,
            self::AES128_CBC,
            self::AES192_CBC,
            self::AES256_CBC,
            self::AES128_CTR,
            self::AES192_CTR,
            self::AES256_CTR,
            self::TWOFISH128_CBC,
            self::TWOFISH192_CBC,
            self::TWOFISH256_CBC,
            self::TWOFISH128_CTR,
            self::TWOFISH192_CTR,
            self::TWOFISH256_CTR => true,
            default => false,
        };
    }

    public function resolve(): ?SymmetricKey
    {
        $encrypt = match ($this) {
            self::NONE => null,
            self::CHACHA20_POLY1305 => new ChaCha20(),
            // blowfish
            self::BLOWFISH_CBC => new Blowfish('cbc'),
            self::BLOWFISH_CTR => new Blowfish('ctr'),
            // 3des
            self::TRIPLE_DES_CBC => new TripleDES('cbc'),
            self::TRIPLE_DES_CTR => new TripleDES('ctr'),
            // arcfour
            self::ARCFOUR, self::ARCFOUR128, self::ARCFOUR256 => new RC4,
            // aes
            self::AES128_GCM, self::AES256_GCM => new Rijndael('gcm'),
            self::AES128_CBC, self::AES192_CBC, self::AES256_CBC => new Rijndael('cbc'),
            self::AES128_CTR, self::AES192_CTR, self::AES256_CTR => new Rijndael('ctr'),
            // twofish
            self::TWOFISH128_CTR, self::TWOFISH192_CTR, self::TWOFISH256_CTR => new Twofish('ctr'),
            self::TWOFISH_CBC, self::TWOFISH128_CBC, self::TWOFISH192_CBC, self::TWOFISH256_CBC => new Twofish('cbc'),
        };
        $encrypt?->disablePadding();
        return $encrypt;
    }

    public function getBlockSize(bool $bits = false): int
    {
        $bytes = match ($this) {
            self::NONE,
            self::ARCFOUR,
            self::ARCFOUR128,
            self::ARCFOUR256,
            self::BLOWFISH_CBC,
            self::BLOWFISH_CTR,
            self::TRIPLE_DES_CBC,
            self::TRIPLE_DES_CTR,
            self::CHACHA20_POLY1305 => 8,
            // 16
            default => 16,
        };
        return $bytes << ($bits ? 3 : 0);
    }

    public function isBadAlgorithmCandidate(): bool
    {
        return match ($this) {
            self::ARCFOUR256,
            self::AES192_CTR,
            self::AES256_CTR => true,
            default => false,
        };
    }

    public function getKeySize(): int
    {
        return match ($this) {
            self::NONE => 0,
            // 16
            self::ARCFOUR,
            self::ARCFOUR128,
            self::AES128_GCM,
            self::AES128_CBC,
            self::AES128_CTR,
            self::BLOWFISH_CBC,
            self::BLOWFISH_CTR,
            self::TWOFISH128_CBC,
            self::TWOFISH128_CTR => 16,
            // 24
            self::AES192_CBC,
            self::AES192_CTR,
            self::TWOFISH192_CTR,
            self::TWOFISH192_CBC,
            self::TRIPLE_DES_CBC,
            self::TRIPLE_DES_CTR => 24,
            // 32
            self::ARCFOUR256,
            self::AES256_GCM,
            self::AES256_CBC,
            self::AES256_CTR,
            self::TWOFISH_CBC,
            self::TWOFISH256_CBC,
            self::TWOFISH256_CTR => 32,
            self::CHACHA20_POLY1305 => 64,
        };
    }

    /**
     * @return list<string>
     */
    public static function getSupported(): array
    {
        return [
            'aes128-gcm@openssh.com',
            'aes256-gcm@openssh.com',
            'aes128-ctr',
            'aes192-ctr',
            'aes256-ctr',
            'aes128-cbc',
            'aes192-cbc',
            'aes256-cbc',
            'chacha20-poly1305@openssh.com',
            'twofish128-ctr',
            'twofish192-ctr',
            'twofish256-ctr',
            'twofish128-cbc',
            'twofish192-cbc',
            'twofish256-cbc',
            'twofish-cbc',
            'blowfish-ctr',
            'blowfish-cbc',
            '3des-ctr',
            '3des-cbc',
            'arcfour256',
            'arcfour128',
            'arcfour',
             'none',
        ];
    }
}
