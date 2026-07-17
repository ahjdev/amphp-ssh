<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Crypt\Common\SymmetricKey;

class SshCipher
{
    protected readonly ?SymmetricKey $key;

    public function __construct(public readonly SshCipherType $type, SshKeyDeriver $derivation, bool $encrypt = false)
    {
        if ($type === SshCipherType::NONE) {
            $this->key = null;
            return;
        }
        $keyBytes = $derivation->resolve($encrypt ? 'C' : 'D');
        $this->key = $type->resolve();
        $this->setKey($keyBytes);
        //
        if (!$type->isAead()) {
            $this->key->enableContinuousBuffer();
        }
        //
        if ($type === SshCipherType::ARCFOUR128 || $type === SshCipherType::ARCFOUR256) {
            $cipher = \str_repeat("\0", 1536);
            $encrypt ? $this->key->encrypt($cipher) : $this->key->decrypt($cipher);
        }
    }

    protected function setKey(string $key): self
    {
        $this->key?->setKey($key);
        return $this;
    }

    public function getBlockSize(bool $bits = false): int
    {
        return $this->type->getBlockSize($bits);
    }

    public function encrypt(string $length, string $packet, ?string $nonce = null): string
    {
        if ($this->type === SshCipherType::NONE) {
            return $packet;
        }

        return $this->key->encrypt($packet);
    }

    public function decrypt(string $length, string $packet, ?string $nonce = null): string
    {
        if ($this->type === SshCipherType::NONE) {
            return $packet;
        }

        return $this->key->decrypt($packet);
    }
}
