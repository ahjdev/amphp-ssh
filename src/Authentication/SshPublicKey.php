<?php declare(strict_types=1);

namespace Amp\Ssh\Authentication;

use phpseclib3\Crypt\PublicKeyLoader;
use phpseclib3\Crypt\Common\PrivateKey;
use phpseclib3\Crypt\Common\PublicKey;
use phpseclib3\Crypt\DSA\PublicKey as DSAPublicKey;
use phpseclib3\Crypt\EC\PublicKey  as ECPublicKey;
use phpseclib3\Crypt\RSA;
use phpseclib3\Crypt\RSA\PublicKey as RSAPublicKey;
use Amp\Ssh\Crypto\SshHostKey;
use Amp\Ssh\Transport\SshPacketHandler;
use Amp\Ssh\Message\UserAuthPkOk;
use Amp\Ssh\Message\UserAuthRequestAskPublicKey;
use Amp\Ssh\Message\UserAuthRequestSignedPublicKey;
use Amp\Ssh\Message\UserAuthSuccess;

class SshPublicKey extends SshAuthentication
{
    public function __construct(
        #[\SensitiveParameter] string $username,
        #[\SensitiveParameter] private string $privateKey,
        #[\SensitiveParameter] private string $passphrase = ''
    ) {
        parent::__construct($username);
    }

    #[\Override]
    final public function authenticate(SshPacketHandler $packetHandler)
    {
        $this->requestService($packetHandler);
        $this->requestPublicKey($packetHandler);
    }

    private function requestPublicKey(SshPacketHandler $packetHandler)
    {
        $supportedAlgos = $packetHandler->getSupportedPublicKeyAlgorithms();
        [$type, $publicKey, $privateKey] = $this->loadKeys($supportedAlgos);
        $blob = $publicKey->toString('OpenSSH', ['binary' => true]);

        // Ask
        $packet = new UserAuthRequestAskPublicKey($this->username, $type->value, $blob);
        $request = $packetHandler->writeMessage($packet);

        if (!$request instanceof UserAuthPkOk) {
            throw new SshAuthenticationFailureException('Authentication Failure');
        }

        // Request
        $packet = new UserAuthRequestSignedPublicKey($this->username, $type->value, $blob);
        $signature = $type->getSignature($packetHandler->getSessionId(), $privateKey, $packet);
        $packet->setSignature($signature);
        $request = $packetHandler->writeMessage($packet);

        if (!$request instanceof UserAuthSuccess) {
            throw new SshAuthenticationFailureException('Authentication Failure');
        }
    }

    /**
     * @return array{0: SshHostKey, 1: PublicKey, 2: PrivateKey}
     */
    private function loadKeys(array $supportedAlgos): array
    {
        $privateKey = PublicKeyLoader::load($this->privateKey, $this->passphrase);

        if (!$privateKey instanceof PrivateKey) {
            throw new SshAuthenticationFailureException('Cannot get private key (maybe wrong passphrase ?)');
        }

        $publicKey  = $privateKey->getPublicKey();

        if ($publicKey instanceof RSAPublicKey) {
            $algorithm = 'ssh-rsa';
            $algos = ['rsa-sha2-256', 'rsa-sha2-512', 'ssh-rsa'];
            foreach ($algos as $algorithm) {
                if (\in_array($algorithm, $supportedAlgos, true)) {
                    break;
                }
            }
            $privateKey = $privateKey->withPadding(RSA::SIGNATURE_PKCS1);
            $type = SshHostKey::from($algorithm);
        } else if ($publicKey instanceof DSAPublicKey) {
            $type = SshHostKey::DSA;
            $privateKey = $privateKey->withSignatureFormat('SSH2');
        } else if ($publicKey instanceof ECPublicKey) {
            $privateKey = $privateKey->withSignatureFormat('SSH2');
            $type = SshHostKey::fromCurve($privateKey->getCurve()); // todo can be array
        } else {
            throw new SshAuthenticationFailureException('Cannot get private key (maybe wrong passphrase ?)');
        }

        $privateKey->withHash($type->getHashAlgorithm());
        return [$type, $publicKey, $privateKey];
    }
}
