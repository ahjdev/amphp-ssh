<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\UserAuthRequest;

abstract class UserAuthRequestPublicKey extends UserAuthRequest
{
    abstract public function hasSignature(): bool;

    public function __construct(
        string $username,
        protected readonly string $algorithm,
        protected readonly string $blob,
        string $serviceName = 'ssh-connection',
    ) {
        parent::__construct($username, $serviceName);
        $this->type = UserAuthRequestType::PUBLIC_KEY;
    }

    #[\Override]
    public function encode(): string
    {
        $payload  = parent::encode();
        $payload .= \pack(
            'CNa*Na*',
            $this->hasSignature(),
            \strlen($this->algorithm), $this->algorithm,
            \strlen($this->blob), $this->blob,
        );

        return $payload;
    }
}
