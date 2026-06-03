<?php declare(strict_types=1);

namespace Amp\Ssh\Authentication;

use Amp\Ssh\Transport\SshPacketHandler;
use Amp\Ssh\Message\UserAuthSuccess;
use Amp\Ssh\Message\UserAuthRequestPassword;
use Amp\Ssh\Authentication\SshAuthenticationFailureException;

final class SshPassword extends SshAuthentication
{
    public function __construct(private readonly string $username, private readonly string $password)
    {
    }

    public function authenticate(string $sessionId, SshPacketHandler $packetHandler)
    {
        $this->requestService($packetHandler);
        $this->requestPassword($packetHandler);
    }

    private function requestPassword(SshPacketHandler $packetHandler)
    {
        $packet = new UserAuthRequestPassword($this->username, $this->password);
        $request = $packetHandler->writeMessage($packet);

        if (!$request instanceof UserAuthSuccess) {
            throw new SshAuthenticationFailureException('Authentication failure');
        }
    }
}
