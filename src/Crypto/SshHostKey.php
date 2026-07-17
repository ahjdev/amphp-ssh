<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Crypt\Common\PrivateKey;
use Amp\Ssh\Message\UserAuthRequestSignedPublicKey;

enum SshHostKey: string
{
    // DSA
    case DSA = 'ssh-dss';

    // RSA
    case RSA_SHA1 = 'ssh-rsa';
    case RSA_SHA2_256 = 'rsa-sha2-256';
    case RSA_SHA2_512 = 'rsa-sha2-512';

    // EdDSA
    case ED25519 = 'ssh-ed25519';
    // case ED448 = 'ssh-ed448';

    // ECDSA
    case ECDSA_SHA2_NISTP256 = 'ecdsa-sha2-nistp256';
    case ECDSA_SHA2_NISTP384 = 'ecdsa-sha2-nistp384';
    case ECDSA_SHA2_NISTP521 = 'ecdsa-sha2-nistp521';

    public function getSignature(
        #[\SensitiveParameter] string $sessionId,
        #[\SensitiveParameter] PrivateKey $privateKey,
        #[\SensitiveParameter] UserAuthRequestSignedPublicKey $message
    ): string {
        $signature = $privateKey->sign(\pack('Na*a*', \strlen($sessionId), $sessionId, $message->encode()));

        if ($this->isRSA()) {
            $name = $this->value;
            $signature = \pack('Na*Na*', \strlen($name), $name, \strlen($signature), $signature);
        }

        return $signature;
    }

    public function isRSA(): bool
    {
        return match ($this) {
            self::RSA_SHA1,
            self::RSA_SHA2_256,
            self::RSA_SHA2_512 => true,
            default => false,
        };
    }

    public function getHashAlgorithm(): string
    {
        return match ($this) {
            self::DSA,
            self::RSA_SHA1 => 'sha1',
            self::ED25519,
            self::RSA_SHA2_512,
            self::ECDSA_SHA2_NISTP521 => 'sha512',
            self::RSA_SHA2_256,
            self::ECDSA_SHA2_NISTP256 => 'sha256',
            self::ECDSA_SHA2_NISTP384 => 'sha384',
        };
    }

    public function verify(string $publicKey, string $signature): bool
    {
        $keyForamt = $this->isRSA() ? 'ssh-rsa' : $this->value;
        return $keyForamt !== $publicKey || $this->value !== $signature;
    }

    public static function fromCurve(string $name): self
    {
        return match ($name) {
            'Ed25519' => self::ED25519,
            'secp256r1' => self::ECDSA_SHA2_NISTP256,
            'secp384r1' => self::ECDSA_SHA2_NISTP384,
            'secp521r1' => self::ECDSA_SHA2_NISTP521,
        };
    }

    /**
     * @return list<string>
     */
    public static function getSupported(): array
    {
        return [
            'ssh-ed25519',
            'ecdsa-sha2-nistp256',
            'ecdsa-sha2-nistp384',
            'ecdsa-sha2-nistp521',
            'rsa-sha2-256',
            'rsa-sha2-512',
            'ssh-rsa',
            'ssh-dss',
        ];
    }
}
