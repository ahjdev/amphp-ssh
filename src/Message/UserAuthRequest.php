<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\Message\UserAuthRequestAskPublicKey;
use Amp\Ssh\Message\UserAuthRequestPassword;
use Amp\Ssh\Message\UserAuthRequestSignedPublicKey;
use Amp\Ssh\Message\UserAuthRequestType;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
    public static function decode(string $data): self
    {
        [$username, $service, $type] = Strings::unpackSSH2('s3', $data);
        $type = UserAuthRequestType::from($type);

        switch ($type) {
            case UserAuthRequestType::PASSWORD:
                [$newPassword, $password] = Strings::unpackSSH2('bs', $data);
                $newPassword =  $newPassword ? Strings::unpackSSH2('s', $data)[0] : null;
                return new UserAuthRequestPassword($username, $password, $newPassword, $service);

            case UserAuthRequestType::PUBLIC_KEY:
                [$hasSignature, $algorithm, $blob] = Strings::unpackSSH2('bs2', $data);
                return $hasSignature
                    ? new UserAuthRequestSignedPublicKey($username, $algorithm, $blob, Strings::unpackSSH2('s', $data)[0], $service)
                    : new UserAuthRequestAskPublicKey($username, $algorithm, $blob, $service);
        };
    }

    #[\Override]
    final public static function getType(): SshMessageType
    {
        return SshMessageType::USERAUTH_REQUEST;
    }
}
