<?php declare(strict_types=1);

namespace Amp\Ssh\Authentication;

use Amp\File;

final class SshPublicKeyFile extends SshPublicKey
{
    public function __construct(
        #[\SensitiveParameter] string $username,
        string $privateKeyPath = '~/.ssh/id_rsa',
        #[\SensitiveParameter] string $passphrase = ''
    ) {
        if (!File\isFile($privateKeyPath)) {
            throw new SshAuthenticationFailureException("PrivateKey file doesn't exists!");
        }
        parent::__construct($username, File\read($privateKeyPath), $passphrase);
    }
}
