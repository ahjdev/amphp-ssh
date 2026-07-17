<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use Amp\Ssh\Message\KexExchangeDhGexInit;
use Amp\Ssh\Message\KeyExchangeDhGexGroup;
use Amp\Ssh\Message\KeyExchangeDhInit;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\DH;
use phpseclib3\Crypt\DH\PrivateKey as DHPrivateKey;
use phpseclib3\Crypt\EC;
use phpseclib3\Crypt\DH\Parameters;
use phpseclib3\Crypt\EC\PrivateKey as ECPrivateKey;
use phpseclib3\Math\BigInteger;

enum SshKeyExchange: string
{
    // sntrup761x25519-sha512@openssh.com
    // mlkem768x25519-sha256
    case CURVE448_SHA512    = 'curve448-sha512';
    case CURVE25519_SHA256  = 'curve25519-sha256';
    case CURVE25519_SHA256_LIBSSH  = 'curve25519-sha256@libssh.org';
    case ECDH_SHA2_NISTP256 = 'ecdh-sha2-nistp256';
    case ECDH_SHA2_NISTP384 = 'ecdh-sha2-nistp384';
    case ECDH_SHA2_NISTP521 = 'ecdh-sha2-nistp521';
    case DIFFIE_HELLMAN_GROUP1_SHA1 = 'diffie-hellman-group1-sha1';
    case DIFFIE_HELLMAN_GROUP14_SHA1   = 'diffie-hellman-group14-sha1';
    case DIFFIE_HELLMAN_GROUP14_SHA256 = 'diffie-hellman-group14-sha256';
    case DIFFIE_HELLMAN_GROUP15_SHA512 = 'diffie-hellman-group15-sha512';
    case DIFFIE_HELLMAN_GROUP16_SHA512 = 'diffie-hellman-group16-sha512';
    case DIFFIE_HELLMAN_GROUP17_SHA512 = 'diffie-hellman-group17-sha512';
    case DIFFIE_HELLMAN_GROUP18_SHA512 = 'diffie-hellman-group18-sha512';
    case DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA1 = 'diffie-hellman-group-exchange-sha1';
    case DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA256 = 'diffie-hellman-group-exchange-sha256';


    public function hash(string $data): string
    {
        return \hash($this->getHashAlgorithm(), $data, true);
    }

    public function getHashLength(bool $bits = false): int
    {
        $bytes = match (true) {
            $this->isSha256() => 32,
            $this->isSha512() => 64,
            $this === self::ECDH_SHA2_NISTP384 => 48,
            default => 20,
        };
        return $bytes << ($bits ? 3 : 0);
    }

    public function getHashAlgorithm(): string
    {
        return match (true) {
            $this->isSha256() => 'sha256',
            $this->isSha512() => 'sha512',
            $this === self::ECDH_SHA2_NISTP384 => 'sha384',
            default => 'sha1',
        };
    }

    private function isSha256(): bool
    {
        return match ($this) {
            self::CURVE448_SHA512,
            self::ECDH_SHA2_NISTP521,
            self::DIFFIE_HELLMAN_GROUP15_SHA512,
            self::DIFFIE_HELLMAN_GROUP16_SHA512,
            self::DIFFIE_HELLMAN_GROUP17_SHA512,
            self::DIFFIE_HELLMAN_GROUP18_SHA512 => true,
            default => false,
        };
    }

    private function isSha512(): bool
    {
        return match ($this) {
            self::ECDH_SHA2_NISTP256,
            self::CURVE25519_SHA256,
            self::CURVE25519_SHA256_LIBSSH,
            self::DIFFIE_HELLMAN_GROUP14_SHA256,
            self::DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA256 => true,
            default => false,
        };
    }

    public function isGroupExchange(): bool
    {
        return $this === self::DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA1 ||
               $this === self::DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA256;
    }

    public function isDiffieHellman(): bool
    {
        return match ($this) {
            self::DIFFIE_HELLMAN_GROUP1_SHA1,
            self::DIFFIE_HELLMAN_GROUP14_SHA1,
            self::DIFFIE_HELLMAN_GROUP14_SHA256,
            self::DIFFIE_HELLMAN_GROUP16_SHA512,
            self::DIFFIE_HELLMAN_GROUP17_SHA512,
            self::DIFFIE_HELLMAN_GROUP18_SHA512,
            self::DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA1,
            self::DIFFIE_HELLMAN_GROUP_EXCHANGE_SHA256 => true,
            default => false,
        };
    }

    public function isEcdh(): bool
    {
        return match ($this) {
            self::CURVE448_SHA512,
            self::ECDH_SHA2_NISTP256,
            self::ECDH_SHA2_NISTP384,
            self::ECDH_SHA2_NISTP521,
            self::CURVE25519_SHA256,
            self::CURVE25519_SHA256_LIBSSH => true,
            default => false,
        };
    }

    public function getCurve(): ?string
    {
        return match ($this) {
            self::CURVE448_SHA512 => 'Curve448',
            self::ECDH_SHA2_NISTP256 => 'nistp256',
            self::ECDH_SHA2_NISTP384 => 'nistp384',
            self::ECDH_SHA2_NISTP521 => 'nistp521',
            self::CURVE25519_SHA256, self::CURVE25519_SHA256_LIBSSH => 'Curve25519',
            default => null,
        };
    }

    private function createFromDhParameters(int $keyLength, Parameters $params)
    {
        if (!$this->isDiffieHellman()) {
            // todo throw exception
        }

        $keyLength = \min($this->getHashLength(), $keyLength); // max($encryptKeyLength, $decryptKeyLength)
        return DH::createKey($params, 16 * $keyLength);
    }

    public function createFromExchangeGrop(int $keyLength, KeyExchangeDhGexGroup $exchangeGroup): DHPrivateKey
    {
        $params = Dh::createParameters(
            new BigInteger($exchangeGroup->prime, -256),
            new BigInteger($exchangeGroup->generator, -256),
        );
        return $this->createFromDhParameters($keyLength, $params);
    }

    public function createPrivateKey(int $keyLength): DHPrivateKey|ECPrivateKey
    {
        if ($this->isEcdh()) {
            return EC::createKey($this->getCurve());
        }

        if ($this->isGroupExchange()) {
            // todo throw exception
        }

        $params = DH::createParameters($this->value);

        return $this->createFromDhParameters($keyLength, $params);
    }

    public function createKeyExchange(ECPrivateKey|DHPrivateKey $privateKey): KeyExchangeDhInit|KexExchangeDhGexInit
    {
        return match (true) {
            $privateKey instanceof ECPrivateKey => new KeyExchangeDhInit(
                $privateKey->getPublicKey()->getEncodedCoordinates()
            ),
            $privateKey instanceof DHPrivateKey => new KexExchangeDhGexInit(
                $privateKey->getPublicKey()->toBigInteger()->toBytes(true)
            ),
        };
    }

    public function compute(DHPrivateKey|EC $private, string|PublicKey $public): string
    {
        $keyBytes = DH::computeSecret($private, $public);

        if (($keyBytes & "\xFF\x80") === "\x00\x00") {
            $keyBytes = \substr($keyBytes, 1);
        } elseif (($keyBytes[0] & "\x80") === "\x80") {
            $keyBytes = "\0$keyBytes";
        }

        return $keyBytes;
    }

    /**
     * @return list<string>
     */
    public static function getSupported(): array
    {
        return [
            'curve25519-sha256',
            'curve25519-sha256@libssh.org',
            'ecdh-sha2-nistp256',
            'ecdh-sha2-nistp384',
            'ecdh-sha2-nistp521',
            'diffie-hellman-group-exchange-sha256',
            'diffie-hellman-group-exchange-sha1',
            'diffie-hellman-group14-sha256',
            'diffie-hellman-group14-sha1',
            'diffie-hellman-group15-sha512',
            'diffie-hellman-group16-sha512',
            'diffie-hellman-group17-sha512',
            'diffie-hellman-group18-sha512',
            'diffie-hellman-group1-sha1',
        ];
    }
}
