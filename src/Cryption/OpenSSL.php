<?php

namespace Amp\Ssh\Cryption;

use Amp\Ssh\Encryption\CipherMode\CipherMode;
use Amp\Ssh\SshCryption;

/**
 * @internal
 */
abstract class OpenSSL implements SshCryption
{
    protected string $key;
    private CipherMode $encryptCipherMode;
    private CipherMode $decryptCipherMode;

    abstract protected function getMethod(): string;

    abstract protected function createCipherMode(string $iv): CipherMode;

    public function resetEncrypt(string $key, string $initIv): self
    {
        $this->key = $key;
        $this->encryptCipherMode = $this->createCipherMode($initIv);
        return $this;
    }

    public function resetDecrypt(string $key, string $initIv): self
    {
        $this->key = $key;
        $this->decryptCipherMode = $this->createCipherMode($initIv);
        return $this;
    }

    public function crypt(string $payload): string
    {
        $cryptedText = \openssl_encrypt(
            $payload,
            $this->getMethod(),
            $this->key,
            \OPENSSL_RAW_DATA | \OPENSSL_NO_PADDING,
            $this->encryptCipherMode->getCurrentIv()
        );

        $this->encryptCipherMode->updateIV($cryptedText);

        return $cryptedText;
    }

    public function decrypt(string $payload): string
    {
        if ((\strlen($payload) % $this->getBlockSize()) !== 0) {
            throw new \RuntimeException('Payload is not a multiple of crypt block size');
        }

        $decrypted = \openssl_decrypt(
            $payload,
            $this->getMethod(),
            $this->key,
            \OPENSSL_RAW_DATA | \OPENSSL_NO_PADDING,
            $this->decryptCipherMode->getCurrentIV()
        );

        $this->decryptCipherMode->updateIV($payload);

        return $decrypted;
    }
}
