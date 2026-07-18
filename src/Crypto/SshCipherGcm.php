<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Common\Functions\Strings;

final class SshCipherGcm extends SshCipherAead
{
    private string $fixedPart;
    private string $invocationCounter;

    public function __construct(SshCipherType $type, SshKeyDeriver $derivation, bool $encrypt = false)
    {
        parent::__construct($type, $derivation, $encrypt);
        $nonce = $derivation->resolveNonce($encrypt ? 'A' : 'B');
        $this->fixedPart = \substr($nonce, 0, 4);
        $this->invocationCounter = \substr($nonce, 4, 8);
    }

    #[\Override]
    public function encrypt(string $length, string $packet, ?string $nonce = null): string
    {
        $this->setupGcm($length);
        return $length . $this->key->encrypt($packet) . $this->key->getTag();
    }

    #[\Override]
    public function decrypt(string $length, string $packet, ?string $nonce = null): string
    {
        $this->setupGcm($length);
        $this->setTag($packet);
        return $this->key->decrypt($packet);
    }

    private function setupGcm(string $length): void
    {
        $this->key->setNonce($this->fixedPart . $this->invocationCounter);
        $this->setAAD($length);
        Strings::increment_str($this->invocationCounter);
    }
}
