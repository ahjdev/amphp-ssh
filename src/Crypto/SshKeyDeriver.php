<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

final class SshKeyDeriver
{
    public function __construct(
        private readonly SshKeyExchange $kex,
        #[\SensitiveParameter] private readonly string $keyBytes,
        #[\SensitiveParameter] private readonly string $exchangeHash,
        #[\SensitiveParameter] private readonly string $sessionId,
    ) {
    }

    public function resolve(string $type, ?int $length = null): string
    {
        $nonce = $this->resolveNonce($type);

        while ($length > \strlen($nonce)) {
            $nonce .= $this->kex->hash($this->keyBytes . $this->exchangeHash . $nonce);
        }

        return \substr($nonce, 0, $length);
    }

    public function resolveNonce(string $type): string
    {
        return $this->kex->hash($this->keyBytes . $this->exchangeHash . $type . $this->sessionId);
    }
}
