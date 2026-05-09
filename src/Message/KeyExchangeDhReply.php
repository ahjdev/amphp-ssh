<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class KeyExchangeCurveReply extends SshMessage
{
    public function __construct(
        public string $hostKey,
        // public string $hostKeyFormat,
        public string $fBytes,
        public string $signature,
        // public string $signatureFormat,
    ) {
    }

    public function encode(): string
    {
        return \pack('CNa*Na*Na*', self::getNumber()->value, $this->hostKey, $this->fBytes, $this->signature);
    }

    public static function decode(): \Generator
    {
        [$hostKey, $fBytes, $signature] = yield from Ssh\times(3, Ssh\string(...));
        // $hostKeyFormat = $fullkey->read_string();
        // $signatureFormat = (new Rfc4253PacketBinary($signature))->read_string();
        // return new static($hostKey, $hostKeyFormat, $fBytes, $signature, $signatureFormat);

        return new static($hostKey, $fBytes, $signature);
    }

    public static function getNumber(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEXDH_REPLY;
    }
}
