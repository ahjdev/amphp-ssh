<?php declare(strict_types=1);

namespace Amp\Ssh\Crypto;

use phpseclib3\Common\Functions\Strings;

abstract class SshCipherAead extends SshCipher
{
    public function __construct(SshCipherType $type, SshKeyDeriver $derivation, bool $encrypt = false)
    {
        parent::__construct($type, $derivation, $encrypt);
    }

    final public function setAAD(string $aad): self
    {
        $this->key->setAAD($aad);
        return $this;
    }

    final public function setTag(string &$packet): self
    {
        $tag = Strings::pop($packet, 16);
        $this->key->setTag($tag);
        return $this;
    }
}
