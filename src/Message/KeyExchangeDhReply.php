<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

class KeyExchangeDhReply extends SshMessage
{
    final public function __construct(
        public readonly string $hostKey,
        public readonly string $hostKeyFormat,
        public readonly string $fBytes,
        public readonly string $signature,
        public readonly string $signatureFormat,
    ) {
    }

    #[\Override]
    final public function encode(): string
    {
        return \pack(
            'CNa*Na*Na*',
            self::getNumber(),
            \strlen($this->hostKey), $this->hostKey,
            \strlen($this->fBytes), $this->fBytes,
            \strlen($this->signature), $this->signature,
        );
    }

    #[\Override]
    final public static function decode(SshBinary $data): self
    {
        $hostKey   = $data->readString();
        $fBytes    = $data->readString();
        $signature = $data->readString();
        //
        $hostKeyFormat = new SshBinary($hostKey);
        $hostKeyFormat = $hostKeyFormat->readString();
        //
        $signatureFormat = new SshBinary($signature);
        $signatureFormat = $signatureFormat->readString();

        return new static($hostKey, $hostKeyFormat, $fBytes, $signature, $signatureFormat);
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEXDH_REPLY;
    }
}
