<?php declare(strict_types=1);

namespace Amp\Ssh\Message;

use Amp\Ssh\SshMessage;
use Amp\Ssh\SshMessageType;
use phpseclib3\Common\Functions\Strings;

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
        return Strings::packSSH2(
            'Ca*L10bN',
            self::getNumber(),
            $this->cookie,
            $this->kex,
            $this->hostKey,
            $this->encryptC2S,
            $this->encryptS2C,
            $this->macC2S,
            $this->macS2C,
            $this->compressC2S,
            $this->compressS2C,
            $this->langC2S,
            $this->langS2C,
            $this->firstPacket,
            0,
        );
    }

    #[\Override]
    public static function decode(string $data): self
    {
        return new static(... Strings::unpackSSH2('a*L10b', $data));
    }

    #[\Override]
    public static function getType(): SshMessageType
    {
        return SshMessageType::KEXINIT;
    }
}
