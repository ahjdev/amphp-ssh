<?php declare(strict_types=1);

namespace Amp\Ssh\Negotiator;

use Amp\Ssh\Crypto\SshMac;
use Amp\Ssh\Crypto\SshCipher;
use Amp\Ssh\Compression\SshCompression;

final class Rfc4253Negotiator implements SshNegotiator
{
    public function __construct(
        public readonly string $sessionId,
        public readonly SshCompression $compress,
        public readonly SshCompression $decompress,
        public readonly ?SshCipher $encrypt = null,
        public readonly ?SshCipher $decrypt = null,
        public readonly ?SshMac $inboundMac = null,
        public readonly ?SshMac $outboundMac = null,
    ) {
    }

    #[\Override]
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    #[\Override]
    public function getEncryption(): ?SshCipher
    {
        return $this->encrypt;
    }

    #[\Override]
    public function getDecryption(): ?SshCipher
    {
        return $this->encrypt;
    }

    #[\Override]
    public function getInboundMac(): ?SshMac
    {
        return $this->inboundMac;
    }

    #[\Override]
    public function getOutboundMac(): ?SshMac
    {
        return $this->outboundMac;
    }

    #[\Override]
    public function getCompress(): SshCompression
    {
        return $this->compress;
    }

    #[\Override]
    public function getDecompress(): SshCompression
    {
        return $this->decompress;
    }
}
