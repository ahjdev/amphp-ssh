<?php declare(strict_types=1);

namespace Amp\Ssh\Authentication;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Common\PrivateKey;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\DSA\PublicKey as DSAPublicKey;
use phpseclib3\Crypt\EC\PublicKey  as ECPublicKey;
use phpseclib3\Crypt\RSA\PublicKey as RSAPublicKey;
use Amp\Ssh\SshHostKey;
use Amp\Ssh\Transport\SshPacketHandler;
use Amp\Ssh\Message\UserAuthPkOk;
use Amp\Ssh\Message\UserAuthRequestAskPublicKey;
use Amp\Ssh\Message\UserAuthRequestSignedPublicKey;
use Amp\Ssh\Message\UserAuthSuccess;

class SshPublicKey extends SshAuthentication
{
    public function __construct(
        private string $username,
        private string $privateKey,
        private string $passphrase = ''
    ) {
    }

    final public function authenticate(string $sessionId, SshPacketHandler $packetHandler)
    {
        $this->requestService($packetHandler);
        $this->requestPublicKey($sessionId, $packetHandler);
    }

    private function requestPublicKey(string $sessionId, SshPacketHandler $packetHandler)
    {
        [$type, $publicKey, $privateKey] = $this->loadKeys();
        $blob = $publicKey->toString('OpenSSH', ['binary' => true]);

        // Ask
        $packet = new UserAuthRequestAskPublicKey($this->username, $type->value, $blob);
        $request = $packetHandler->writeMessage($packet);

        if (!$request instanceof UserAuthPkOk) {
            throw new SshAuthenticationFailureException('Authentication Failure');
        }

        // Request
        $packet = new UserAuthRequestSignedPublicKey($this->username, $type->value, $blob);
        $signature = $type->getSignature($sessionId, $packet, $privateKey);
        $packet->setSignature($signature);
        $request = $packetHandler->writeMessage($packet);

        if (!$request instanceof UserAuthSuccess) {
            throw new SshAuthenticationFailureException('Authentication Failure');
        }
    }

    /**
     * @return array{0: SshHostKey, 1: PublicKey, 2: PrivateKey}
     */
    private function loadKeys(): array
    {
        $privateKey = PublicKeyLoader::load($this->privateKey, $this->passphrase);

        if (!$privateKey instanceof PrivateKey) {
            throw new SshAuthenticationFailureException('Cannot get private key (maybe wrong passphrase ?)');
        }

        $publicKey  = $privateKey->getPublicKey();

        if ($publicKey instanceof RSAPublicKey) {
            $privateKey = $privateKey->withPadding(RSA::SIGNATURE_PKCS1);
            $type = SshHostKey::RSA_SHA1;
        } else if ($publicKey instanceof DSAPublicKey) {
            $type = SshHostKey::DSA;
            $privateKey = $privateKey->withSignatureFormat('SSH2');
        } else if ($publicKey instanceof ECPublicKey) {
            $privateKey = $privateKey->withSignatureFormat('SSH2');
            $type = SshHostKey::fromCurve($privateKey->getCurve()); // todo can be array
        } else {
            // todo:// add exception
        }

        $privateKey->withHash($type->getHashAlgorithm());
        return [$type, $publicKey, $privateKey];
    }
}
