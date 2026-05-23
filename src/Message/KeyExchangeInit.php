<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh;
use Amp\Ssh\SshBinary;
use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;

final class KeyExchangeInit extends SshMessage
{
    public readonly string $cookie;

    public function __construct(
        ?string $cookie = null,
        public readonly array $kex = [],
        public readonly array $hostKey = [],
        public readonly array $encryptC2S = [],
        public readonly array $encryptS2C = [],
        public readonly array $macC2S = [],
        public readonly array $macS2C = [],
        public readonly array $compressC2S = [],
        public readonly array $compressS2C = [],
        public readonly array $langC2S = [],
        public readonly array $langS2C = [],
        public readonly bool $firstPacket = false,
    ) {
        $this->cookie = $cookie ??= \random_bytes(16);
    }

    #[\Override]
    public function encode(): string
    {
        $kex         = $this->toNameList($this->kex);
        $hostKey     = $this->toNameList($this->hostKey);
        $encryptC2S  = $this->toNameList($this->encryptC2S);
        $encryptS2C  = $this->toNameList($this->encryptS2C);
        $macC2S      = $this->toNameList($this->macC2S);
        $macS2C      = $this->toNameList($this->macS2C);
        $compressC2S = $this->toNameList($this->compressC2S);
        $compressS2C = $this->toNameList($this->compressS2C);
        $langC2S     = $this->toNameList($this->langC2S);
        $langS2C     = $this->toNameList($this->langS2C);

        return \pack(
            'Ca*Na*Na*Na*Na*Na*Na*Na*Na*Na*Na*CN',
            self::getNumber(),
            $this->cookie,
            \strlen($kex), $kex,
            \strlen($hostKey), $hostKey,
            \strlen($encryptC2S), $encryptC2S,
            \strlen($encryptS2C), $encryptS2C,
            \strlen($macC2S), $macC2S,
            \strlen($macS2C), $macS2C,
            \strlen($compressC2S), $compressC2S,
            \strlen($compressS2C), $compressS2C,
            \strlen($langC2S), $langC2S,
            \strlen($langS2C), $langS2C,
            $this->firstPacket, 0
        );
    }

    #[\Override]
    public static function decode(SshBinary $data): self
    {
        $kex = $data->readString();
        $hostKey = $data->readNamelist();
        // Encryption
        $encryptC2S = $data->readNamelist();
        $encryptS2C = $data->readNamelist();
        // Mac
        $macC2S = $data->readNamelist();
        $macS2C = $data->readNamelist();
        // Compress
        $compressC2S = $data->readNamelist();
        $compressS2C = $data->readNamelist();
        // Lang
        $langC2S = $data->readNamelist();
        $langS2C = $data->readNamelist();

        return new static(
            $kex, $hostKey,
            $encryptC2S, $encryptS2C, $macC2S, $macS2C,
            $compressC2S, $compressS2C, $langC2S, $langS2C,
        );
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::SSH_MSG_KEXINIT;
    }
}
