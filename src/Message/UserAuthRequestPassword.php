<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\UserAuthRequest;

final class UserAuthRequestPassword extends UserAuthRequest
{
    public function __construct(
        string $username,
        #[\SensitiveParameter] private readonly string $password = '',
        private readonly ?string $newPassword = null,
        string $serviceName = 'ssh-connection',
    ) {
        parent::__construct($username, $serviceName);
        $this->type = UserAuthRequestType::PASSWORD;
    }

    #[\Override]
    public function encode(): string
    {
        $payload  = parent::encode();
        $password = \pack('Na*', \strlen($this->password), $this->password);

        if ($this->newPassword === null) {
            $payload .= \pack('C', 0);
            $payload .= $password;
        } else {
            $payload .= \pack('C', 1);
            $payload .= $password;
            $payload .= \pack('Na*', \strlen($this->newPassword), $this->newPassword);
        }

        return $payload;
    }
}
