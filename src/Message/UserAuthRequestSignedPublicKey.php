<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

final class UserAuthRequestSignedPublicKey extends UserAuthRequestPublicKey
{
    public function __construct(
        string $username,
        string $algorithm,
        string $blob,
        #[\SensitiveParameter] private ?string $signature = null,
        string $serviceName = 'ssh-connection',
    ) {
        parent::__construct($username, $serviceName, $algorithm, $blob);
    }

    #[\Override]
    public function hasSignature(): bool
    {
        return true;
    }

    public function setSignature(#[\SensitiveParameter] string $signature): self
    {
        $this->signature = $signature;
        return $this;
    }

    #[\Override]
    public function encode(): string
    {
        $payload = parent::encode();

        if (!\empty($this->signature)) {
            $payload .= \pack('Na*', \strlen($this->signature), $this->signature);
        }

        return $payload;
    }
}
