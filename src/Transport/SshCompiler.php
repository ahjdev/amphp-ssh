<?php declare(strict_types=1);

namespace Amp\Ssh\Transport;

use Amp\Ssh\Crypto\SshMac;
use Amp\Ssh\Crypto\SshCipher;
use Amp\Ssh\Compression\SshCompression;

interface SshCompiler
{
    /**
     * Provides stateful compilation of ssh packets.
     */
    public function compilePacket(int $seqNumber, string $data, SshCompression $compression, ?SshCipher $encryption = null, ?SshMac $mac = null): string;
}
