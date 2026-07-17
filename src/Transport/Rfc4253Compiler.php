<?php declare(strict_types=1);

namespace Amp\Ssh\Transport;

use Amp\ForbidCloning;
use Amp\ForbidSerialization;
use Amp\Ssh\Crypto\SshCipher;
use Amp\Ssh\Crypto\SshMac;
use Amp\Ssh\Compression\SshCompression;
use Amp\Ssh\Crypto\SshCipherAead;

final class Rfc4253Compiler implements SshCompiler
{
    use ForbidCloning;
    use ForbidSerialization;

    #[\Override]
    public function compilePacket(int $seqNumber, string $data, SshCompression $compression, ?SshCipher $encryption = null, ?SshMac $mac = null): string
    {
        $data = $compression->compress($data);
        $length = \strlen($data) + 1;
        $isAead = $encryption instanceof SshCipherAead;
        $isEtm = $mac->isEtm();

        /**
         * [packet_length]-[padding_length]-[payload]-[random_padding]-[mac/tag]
         * uint32     packet_length
         * byte       padding_length
         * byte[n1]   payload; n1 = packet_length - padding_length - 1
         * byte[n2]   random padding; n2 = padding_length
         * uint32[16] mac/tag
         */
        $length += ($isAead || $isEtm) ? 0 : 4;
        $blockSize = (int) $encryption->getBlockSize();
        $padLength = $blockSize - ($length % $blockSize);
        $padLength += $padLength < 4 ? $blockSize : 0;
        $packetLength = \strlen($data) + $padLength + 1;
        $packetLength = \pack('N', $packetLength);
        $packet = \pack('Ca*a*', $padLength, $data, \random_bytes($padLength));

        if ($isAead) {
            $nonce = \pack('N2', 0, $seqNumber);
            $packet = $encryption->encrypt($packetLength, $packet, $nonce);
        } elseif ($isEtm) {
            $nonce = \pack('N2', 0, $seqNumber);
            $packet = $packetLength . $encryption->encrypt($packetLength, $packet, $nonce);
            $packet .= $mac->compute($seqNumber, $packet);
        } else {
            $packet = $packetLength . $packet;
            $macValue = $mac->compute($seqNumber, $packet);
            $packet = $encryption->encrypt($packetLength, $packet);
            $packet .= $macValue;
        }

        return $packet;
    }
}
