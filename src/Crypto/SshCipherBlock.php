<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

final class SshCipherBlock extends SshCipher
{
    public function __construct(SshCipherType $type, SshKeyDeriver $derivation, bool $encrypt = false)
    {
        parent::__construct($type, $derivation, $encrypt);
        $blockSize = $type->getBlockSize();
        $this->key->setIV($derivation->resolve($encrypt ? 'A' : 'B', $blockSize));
    }
}
