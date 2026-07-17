<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Crypt\ChaCha20;
use phpseclib3\Crypt\Common\SymmetricKey;

/**
 * @property ChaCha20 $key
 */
final class SshCipherChaCha20 extends SshCipherAead
{
    private readonly SymmetricKey $lengthKey;

    public function __construct(SshCipherType $type, SshKeyDeriver $derivation, bool $encrypt = false)
    {
        parent::__construct($type, $derivation, $encrypt);
    }

    public function getLength(string $nonce, string $encryptedLength): int
    {
        $this->lengthKey->setNonce($nonce);
        $length = $this->lengthKey->decrypt($encryptedLength);
        return \unpack('N', $length)[1];
    }

    #[\Override]
    public function encrypt(string $length, string $packet, ?string $nonce = null): string
    {
        $this->lengthKey->setNonce($nonce);
        $encryptedLength = $this->lengthKey->encrypt($length);
        $this->setupPoly1305($nonce, $encryptedLength);
        return $encryptedLength . $this->key->encrypt($packet) . $this->key->getTag();
    }

    #[\Override]
    public function decrypt(string $length, string $packet, ?string $nonce = null): string
    {
        $this->setupPoly1305($nonce, $length);
        $this->setTag($packet);
        return $this->key->decrypt($packet);
    }

    #[\Override]
    protected function setKey(string $key): self
    {
        $this->lengthKey = $this->type->resolve();
        $this->lengthKey->setKey(\substr($key, 32, 32));
        $this->key->setKey(\substr($key, 0, 32));
        return $this;
    }

    private function setupPoly1305(string $nonce, string $encryptedLength): void
    {
        $this->key->setNonce($nonce);
        $this->key->setCounter(0);
        $this->key->setPoly1305Key($this->key->encrypt(\str_repeat("\0", 32)));
        $this->setAAD($encryptedLength);
        $this->key->setCounter(1);
    }
}
