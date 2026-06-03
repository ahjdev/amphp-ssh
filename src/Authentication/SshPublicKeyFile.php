<?php declare(strict_types=1);

namespace Amp\Ssh\Authentication;

use Amp\File;

final class SshPublicKeyFile extends SshPublicKey
{
    public function __construct(
        private string $username,
        private string $privateKeyPath = '~/.ssh/id_rsa',
        private string $passphrase = ''
    ) {
        parent::__construct($username, File\read($this->privateKeyPath), $passphrase);
    }
}
