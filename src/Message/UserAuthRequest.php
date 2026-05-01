<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\UserAuthRequestAskPublicKey;
use Amp\Ssh\Message\UserAuthRequestPassword;
use Amp\Ssh\Message\UserAuthRequestSignedPublicKey;
use Amp\Ssh\Message\UserAuthRequestType;
use Amp\Ssh\SshMessage;

abstract class UserAuthRequest extends SshMessage
{
    protected readonly UserAuthRequestType $type;

    public function __construct(
        public readonly string $username,
        public readonly string $serviceName = 'ssh-connection',
    ) {
    }

    public function encode(): string
    {
        $type = $this->type->value;
        return \pack(
            'C*Na*Na*Na', self::getNumber(),
            \strlen($this->username), $this->username,
            \strlen($this->serviceName), $this->serviceName,
            \strlen($type), $type
        );
    }

    public static function decode(): \Generator
    {
        [$username, $service, $type] = yield from Ssh\times(3, Ssh\string(...));
        $type = UserAuthRequestType::from($type);

        switch ($type)
        {
            case UserAuthRequestType::PASSWORD:
                $newPassword = yield from Ssh\boolean();
                $password    = yield from Ssh\string();
                $newPassword =  $newPassword ? yield from Ssh\string() : null;
                return new UserAuthRequestPassword($username, $password, $newPassword, $service);

            case UserAuthRequestType::PUBLIC_KEY:
                $hasSignature = yield from Ssh\boolean();
                [$algorithm, $blob]= yield from Ssh\times(2, Ssh\string(...));

                return $hasSignature
                    ? new UserAuthRequestSignedPublicKey($username, $algorithm, $blob, yield from Ssh\string(), $service)
                    : new UserAuthRequestAskPublicKey($username, $algorithm, $blob, $service);
        };
    }

    final public static function getNumber(): int
    {
        return self::SSH_MSG_USERAUTH_REQUEST;
    }
}
