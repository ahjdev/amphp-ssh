<?php declare(strict_types=1);

namespace Amp\Ssh;

interface SshCryption extends SshDecryption, SshEncryption
{
    public function getName(): string;

    public function getKeySize(): int;

    public function getBlockSize(): int;
}
