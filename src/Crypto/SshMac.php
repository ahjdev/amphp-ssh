<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Crypt\Hash;

final class SshMac
{
    private readonly ?Hash $mac;

    private readonly bool $isEtm;

    public function __construct(public readonly SshMacType $type, SshKeyDeriver $derivation, bool $outbound = false)
    {
        $this->mac = $type->resolve();
        $this->isEtm = $type->isEtm();

        if ($type !== SshMacType::NONE) {
            $tag = $outbound ? 'E' : 'F';
            $this->mac->setKey($derivation->resolve($tag, $type->getLength()));
        }
    }

    public function isEtm(): bool
    {
        return $this->isEtm;
    }

    public function getLength(): int
    {
        return $this->type->getLength();
    }

    public function compute(int $seqNumber, string $packet): string
    {
        if ($this->type === SshMacType::NONE) {
            return '';
        }

        if ($this->type->isUmac()) {
            $this->mac->setNonce(\pack('N2', 0, $seqNumber));
        }

        $data = \pack('N2a*', $seqNumber, \strlen($packet), $packet);
        return $this->mac->hash($data);
    }

    public function verify(int $seqNumber, string $packet, string $mac): bool
    {
        $expected = $this->compute($seqNumber, $packet);
        return \hash_equals($expected, $mac);
    }
}
