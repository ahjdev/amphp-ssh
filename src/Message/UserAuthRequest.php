<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\Message\UserAuthRequestAskPublicKey;
use Amp\Ssh\Message\UserAuthRequestPassword;
use Amp\Ssh\Message\UserAuthRequestSignedPublicKey;
use Amp\Ssh\Message\UserAuthRequestType;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use Amp\Ssh\Transport\SshParserException;

abstract class UserAuthRequest extends SshMessage
{
    protected readonly UserAuthRequestType $type;

    public function __construct(
        public readonly string $username, public readonly string $serviceName = 'ssh-connection',
    ) {
    }

    #[\Override]
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

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $username = $data->readString();
        $service = $data->readString();
        $type = UserAuthRequestType::from($data->readString());

        switch ($type) {
            case UserAuthRequestType::PASSWORD:
                $newPassword = $data->readBoolean();
                $password    = $data->readString();
                $newPassword =  $newPassword ? $data->readString() : null;
                return new UserAuthRequestPassword($username, $password, $newPassword, $service);

            case UserAuthRequestType::PUBLIC_KEY:
                $hasSignature = $data->readBoolean();
                $algorithm    = $data->readString();
                $blob         = $data->readString();

                return $hasSignature
                    ? new UserAuthRequestSignedPublicKey($username, $algorithm, $blob, $data->readString(), $service)
                    : new UserAuthRequestAskPublicKey($username, $algorithm, $blob, $service);
        };
    }

    #[\Override]
    final public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_USERAUTH_REQUEST;
    }
}
