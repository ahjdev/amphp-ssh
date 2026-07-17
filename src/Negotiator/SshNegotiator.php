<?php declare(strict_types=1);

namespace Amp\Ssh\Negotiator;

use Amp\Ssh\Compression\SshCompression;
use Amp\Ssh\Crypto\SshCipher;
use Amp\Ssh\Crypto\SshMac;

interface SshNegotiator
{
    public function getEncryption(): ?SshCipher;
    public function getDecryption(): ?SshCipher;
    public function getInboundMac(): ?SshMac;
    public function getOutboundMac(): ?SshMac;
    public function getCompress(): SshCompression;
    public function getDecompress(): SshCompression;
}
